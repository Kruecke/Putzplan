#include <boost/date_time/gregorian/gregorian.hpp>
#include <boost/program_options.hpp>
#include <fstream>
#include <iomanip>
#include <string>
#include <vector>
// Debug:
#include <iostream>

namespace cal = boost::gregorian;
namespace po  = boost::program_options;

int main (int argc, char **argv) {
    // ----- Feste Einstellungen -----
    const int weeks_to_print = 15; // Anzahl der Wochen (Zeilen) in der Vorlage
    const std::vector<std::string> rooms = {
        "\\aaa", "\\bbb", "\\ccc", "\\ddd", "\\eee", "\\fff"};
    const int task_cols = 5;

    // Helfer zum Rechnen mit Wochen
    const cal::first_day_of_the_week_before fwdbf(cal::Monday);

    // In der Woche vom 01.01.2015 startete Zimmer 101
    // ('\aaa' in der LaTeX Vorlage) mit der ersten Aufgabe.
    const auto start = fwdbf.get_date(cal::date(2015, 1, 1));

    const auto today = cal::date(cal::day_clock::local_day());
    const auto begin = today.day_of_week() == cal::Monday ? today : fwdbf.get_date(today);

    // ----- Programm Optionen ----
    po::options_description desc("Programm Optionen");
    desc.add_options()
        ("Ausnahmen", po::value<std::vector<int>>()->multitoken(),
            "z.B. 'S102 = 4' für keine Aufgabe 4 für S102.");

    po::variables_map vm;
    std::ifstream config("config.ini");
    po::store(po::parse_config_file(config, desc), vm);
    po::notify(vm);
    // TODO...

    // Debug:
    for (auto option : vm)
        std::cout << option.first << " = " << option.second.as<std::string>() << "\n";

    // ----- Generiere Plan -----
    for (std::string seite : {"r", "s"}) {
        cal::week_iterator week_it(begin);
        std::ofstream fout("dates_" + seite + ".txt", std::ios::trunc);

        for (int i = 0; i < weeks_to_print; ++i) {
            const auto week_begin = *week_it;
            const auto week_end   = *++week_it - cal::days(1);

            fout << std::setw(2) << std::setfill('0') << week_begin.week_number() // Kalenderwoche
                 << " & " << std::setw(2) << std::setfill('0') << (int) week_begin.day()   << "."
                          << std::setw(2) << std::setfill('0') << (int) week_begin.month() << "."
                 << " & " << std::setw(2) << std::setfill('0') << (int) week_end.day()     << "."
                          << std::setw(2) << std::setfill('0') << (int) week_end.month()   << ".";

            const int diff = (week_begin - start).days() / 7;
            for (int j = 0; j < task_cols; ++j) {
                const int index = (j - diff) % (int) rooms.size();
                fout << " & " << rooms[(index + rooms.size()) % rooms.size()];
            }

            fout << " \\\\\n";
        }
    }

	//system("pause");
    return 0;
}

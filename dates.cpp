#include <boost/date_time/gregorian/gregorian.hpp>
#include <boost/program_options.hpp>
#include <fstream>
#include <iomanip>
#include <map>
#include <string>
#include <vector>
// Debug:
#include <iostream>

namespace cal = boost::gregorian;
namespace po  = boost::program_options;

void schedule(std::ofstream &fout) {
    // ----- Feste Einstellungen -----
    const int weeks_to_print = 15; // Anzahl der Wochen (Zeilen) in der Vorlage
    const std::vector<std::string> rooms_tex = {
        "\\aaa", "\\bbb", "\\ccc", "\\ddd", "\\eee", "\\fff"};
    const int task_cols = 5;

    // Helfer zum Rechnen mit Wochen
    const cal::first_day_of_the_week_before fwdbf(cal::Monday);

    // In der Woche vom 01.01.2015 startete Zimmer 101
    // ('\aaa' in der LaTeX Vorlage) mit der ersten Aufgabe.
    const auto start = fwdbf.get_date(cal::date(2015, 1, 1));

    const auto today = cal::date(cal::day_clock::local_day());
    const auto begin = today.day_of_week() == cal::Monday ? today : fwdbf.get_date(today);

    // ----- Generiere Plan -----
    cal::week_iterator week_it(begin);
    for (int i = 0; i < weeks_to_print; ++i) {
        const auto week_begin = *week_it;
        const auto week_end   = *++week_it - cal::days(1);

        // Wochenkopf (linke Spalten)
        fout << std::setw(2) << std::setfill('0') << week_begin.week_number() // Kalenderwoche
             << " & " << std::setw(2) << std::setfill('0') << (int) week_begin.day()   << "."
                      << std::setw(2) << std::setfill('0') << (int) week_begin.month() << "."
             << " & " << std::setw(2) << std::setfill('0') << (int) week_end.day()     << "."
                      << std::setw(2) << std::setfill('0') << (int) week_end.month()   << ".";

        const int diff = (week_begin - start).days() / 7;
        for (int j = 0; j < task_cols; ++j) {
            const int rsize = rooms_tex.size();
            const int index = (((j - diff) % rsize) + rsize) % rsize;

            fout << " & " << rooms_tex[index];
        }

        fout << " \\\\\n";
    }
}

int main(int argc, char **argv) {
    // ----- Programm Optionen ----
    po::options_description desc("Programm Optionen");
    std::map<std::string, std::vector<int>> exceptions;
    for (std::string site : {"R", "S"})
        for (std::string number : {"101", "102", "103", "104", "105", "106"})
            desc.add_options()(("Ausnahmen." + site + number).c_str(),
                po::value<std::vector<int>>(&exceptions[site + number])->multitoken(), "...");

    po::variables_map vm;
    std::ifstream config("config.ini");
    po::store(po::parse_config_file(config, desc), vm);
    po::notify(vm);

    // Debug:
    //std::cout << desc << "\n";
    for (auto kv : exceptions) {
        std::cout << kv.first << " =";
        for (auto v : kv.second)
            std::cout << " " << v;
        std::cout << "\n";
    }

    // ----- Generiere Plan -----
    for (char site : {'R', 'S'}) {
        std::ofstream fout(std::string("dates_")
            + (char) std::tolower(site) + ".txt", std::ios::trunc);

        schedule(fout);
    }

    return 0;
}

#include <algorithm>
#include <boost/date_time/gregorian/gregorian.hpp>
#include <boost/program_options.hpp>
#include <fstream>
#include <iomanip>
#include <map>
#include <stdexcept>
#include <string>
#include <vector>

namespace cal = boost::gregorian;
namespace po  = boost::program_options;

typedef std::vector<std::vector<int>> except_room_tasks_t;

// Hilfskonstrukt, um Räume für Aufgaben zu finden
// und vergangene Einsprünge anderer Räume zu berücksichtigen.
class helpout_map {
public:
    helpout_map(const std::vector<std::string> &rooms,
                const except_room_tasks_t      &exceptions)
        : m_rooms(rooms), m_exceptions(exceptions)
    {
        for (std::size_t i = 0; i < m_rooms.size(); ++i)
            m_helpedout.emplace_back(i, 0);
    }

    std::string schedule_room_for_task(int room_index, int task_index) {
        const auto &except = m_exceptions[room_index];
        if (std::find(except.begin(), except.end(), task_index + 1) == except.end()) {
            // Wenn der Raum die Aufgabe machen kann, wird er eingeteilt.
            return m_rooms[room_index];
        }

        // Ansonsten, finde Ersatz.
        auto helped = m_helpedout; // Kopie
        sort(helped.begin(), helped.end(), // Sortiere nach Häufigkeit des Einspringens.
            [](const std::pair<int, int> &a, const std::pair<int, int> &b) {
                if (a.second != b.second)
                    return a.second < b.second;
                else
                    return a.first < b.first;
            });
        // Schaue, wer einspringen kann.
        for (auto p : helped) {
            const auto &except = m_exceptions[p.first];
            if (std::find(except.begin(), except.end(), task_index + 1) == except.end()) {
                // Ersatz gefunden!
                ++m_helpedout[p.first].second; // Einsprung merken
                return m_rooms[p.first];
            }
        }

        // Bis hierhin sollte immer ein Ersatz gefunden worden sein!
        throw std::logic_error("Impossible schedule!");
    }

private:
    const std::vector<std::string>   m_rooms;
    const except_room_tasks_t        m_exceptions;
    std::vector<std::pair<int, int>> m_helpedout;
};

void schedule(std::ofstream &fout, const except_room_tasks_t &exceptions) {
    // ----- Feste Einstellungen -----
    const int weeks_to_print = 15; // Anzahl der Wochen (Zeilen) in der Vorlage
    const std::vector<std::string> rooms_tex = {
        "\\aaa", "\\bbb", "\\ccc", "\\ddd", "\\eee", "\\fff"};
    const int task_cols = 5;

    // Helfer zum Rechnen mit Wochen
    const cal::first_day_of_the_week_before fmondaybf(cal::Monday);

    // In der Woche vom 01.01.2015 startete Zimmer 101
    // ('\aaa' in der LaTeX Vorlage) mit der ersten Aufgabe.
    const auto start = fmondaybf.get_date(cal::date(2015, 1, 1)); // Montag vor dem 01.01.2015

    const auto today = cal::date(cal::day_clock::local_day());
    const auto begin = today.day_of_week() == cal::Monday ? today : fmondaybf.get_date(today);

    helpout_map helpout(rooms_tex, exceptions);

    // ----- Vorlauf -----
    // Ermittle, wie oft andere Räume bereits eingesprungen sind,
    // damit gegenwärtige Pläne konsistent sind.
    for (auto past_it = cal::week_iterator(start); *past_it != begin; ++past_it) {
        const int diff = (*past_it - start).days() / 7;
        for (int task = 0; task < task_cols; ++task) {
            const int rsize = rooms_tex.size();
            const int index = (((task - diff) % rsize) + rsize) % rsize;

            // Verplane Raum für Aufgabe, ignoriere Ergebnis.
            helpout.schedule_room_for_task(index, task);
        }
    }

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

        // Zuweisung der Aufgaben in der Woche.
        const int diff = (week_begin - start).days() / 7;
        for (int task = 0; task < task_cols; ++task) {
            const int rsize = rooms_tex.size();
            const int index = (((task - diff) % rsize) + rsize) % rsize;

            // Verplane Raum für Aufgabe.
            fout << " & " << helpout.schedule_room_for_task(index, task);
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

    // ----- Generiere Plan -----
    for (char site : {'R', 'S'}) {
        std::ofstream fout(std::string("dates_")
            + (char) std::tolower(site) + ".txt", std::ios::trunc);

        // Ausnahmen nach Seite filtern.
        except_room_tasks_t except;
        for (auto &kv : exceptions)
            if (kv.first[0] == site)
                except.push_back(kv.second); // Reihenfolge stimmt, da map ordered ist.

        schedule(fout, except);
    }

    return 0;
}

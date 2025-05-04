#include <iostream>
#include <fstream>
#include <string>
#include <vector>
#include <sstream>
#include <unordered_map>
#include <iomanip>
#include <ctime>
#include <algorithm>

using namespace std;

// Structure for patient details
struct Patient {
    string name;
    string appointmentDate;
    string doctor;
    string symptoms;
    int emergencyLevel;
    string appointmentTime;
};

class HospitalQueue {
    unordered_map<string, vector<Patient>> doctorPatients; // Doctor -> list of patients

public:

    void loadFromCSV(const string& filename) {
        ifstream file(filename);
        if (!file.is_open()) {
            cout << "Could not open file: " << filename << endl;
            return;
        }

        string line;
        while (getline(file, line)) {
            stringstream ss(line);
            string name, date, doctor, symptoms, emergencyStr, time;
            getline(ss, name, ',');
            getline(ss, date, ',');
            getline(ss, doctor, ',');
            getline(ss, symptoms, ',');
            getline(ss, emergencyStr, ',');
            getline(ss, time, ',');

            if (name.empty() || doctor.empty()) continue;

            int emergencyLevel = stoi(emergencyStr);

            Patient p{name, date, doctor, symptoms, emergencyLevel, ""};
            doctorPatients[doctor].push_back(p);
        }

        file.close();
    }

    string getNextDate(const string& currentDate) {
        tm timeStruct = {};
        istringstream ss(currentDate);
        ss >> get_time(&timeStruct, "%Y-%m-%d");

        if (ss.fail()) {
            cerr << "Failed to parse date: " << currentDate << endl;
            return currentDate;
        }

        timeStruct.tm_mday += 1;
        mktime(&timeStruct);

        ostringstream out;
        out << put_time(&timeStruct, "%Y-%m-%d");
        return out.str();
    }

    void assignTimes() {
        for (auto& entry : doctorPatients) {
            auto& patientsList = entry.second;

            sort(patientsList.begin(), patientsList.end(), [](const Patient& a, const Patient& b) {
                return a.emergencyLevel > b.emergencyLevel;
            });

            string currentDate = patientsList.empty() ? "" : patientsList[0].appointmentDate;
            int startHour = 10;
            int startMinute = 0;

            for (size_t i = 0; i < patientsList.size(); ++i) {
                int requiredMinutes = 30;
                if (patientsList[i].emergencyLevel == 5)
                    requiredMinutes = 120;
                else if (patientsList[i].emergencyLevel == 4)
                    requiredMinutes = 60;

                int currentTotalMinutes = startHour * 60 + startMinute;
                int closingTimeMinutes = 19 * 60;

                if (currentTotalMinutes + requiredMinutes > closingTimeMinutes) {
                    currentDate = getNextDate(currentDate);
                    startHour = 10;
                    startMinute = 0;
                }

                // Format Start Time
                stringstream ssStart;
                bool isStartPM = startHour >= 12;
                int displayStartHour = (startHour > 12) ? startHour - 12 : (startHour == 0 ? 12 : startHour);
                ssStart << setw(2) << setfill('0') << displayStartHour << ":"
                        << setw(2) << setfill('0') << startMinute
                        << (isStartPM ? " PM" : " AM");
                string startTimeStr = ssStart.str();

                // Format End Time
                int endHour = startHour + (startMinute + requiredMinutes) / 60;
                int endMinute = (startMinute + requiredMinutes) % 60;
                bool isEndPM = endHour >= 12;
                int displayEndHour = (endHour > 12) ? endHour - 12 : (endHour == 0 ? 12 : endHour);

                stringstream ssEnd;
                ssEnd << setw(2) << setfill('0') << displayEndHour << ":"
                      << setw(2) << setfill('0') << endMinute
                      << (isEndPM ? " PM" : " AM");
                string endTimeStr = ssEnd.str();

                patientsList[i].appointmentTime = startTimeStr + " - " + endTimeStr;
                patientsList[i].appointmentDate = currentDate;

                startHour = endHour;
                startMinute = endMinute;
            }
        }
    }

    void writeIndexedCSV(const string& filename) {
        ofstream file(filename);
        if (!file.is_open()) {
            cout << "Could not open file for writing: " << filename << endl;
            return;
        }

        // Write headers
        file << "Turn,Name,Date,Doctor,Symptoms,Priority,AssignedTime\n";

        // Sort doctors alphabetically
        vector<string> sortedDoctors;
        for (auto& entry : doctorPatients)
            sortedDoctors.push_back(entry.first);

        sort(sortedDoctors.begin(), sortedDoctors.end());

        for (auto& doctor : sortedDoctors) {
            auto& patientsList = doctorPatients[doctor];
            int turn = 1;

            for (auto& p : patientsList) {
                file << turn++ << ","
                     << p.name << ","
                     << p.appointmentDate << ","
                     << p.doctor << ","
                     << p.symptoms << ","
                     << p.emergencyLevel << ","
                     << p.appointmentTime << "\n";
            }
        }

        file.close();
        cout << "\nAppointments scheduled and written successfully to: " << filename << endl;
    }
};

int main() {
    HospitalQueue hq;
    hq.loadFromCSV("appointments.csv");      // Input CSV
    hq.assignTimes();
    hq.writeIndexedCSV("appointments_output.csv"); // Output CSV
    return 0;
}

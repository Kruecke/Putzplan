CXXFLAGS := -std=c++11 -Wall
LDLIBS   := -lboost_program_options

all: Putzplan.pdf

Putzplan.pdf: Putzplan.tex dates_r.txt dates_s.txt
	pdflatex $<

dates_r.txt dates_s.txt: dates config.ini
	./$<

dates: dates.cpp

.PHONY: clean
clean:
	rm -f dates
	rm -f dates_r.txt
	rm -f dates_s.txt
	rm -f Putzplan.log
	rm -f Putzplan.aux

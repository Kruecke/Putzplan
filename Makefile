all: Putzplan.pdf

Putzplan.pdf: Putzplan.tex dates_r.txt dates_s.txt
	pdflatex $<

dates_r.txt dates_s.txt: dates config.txt
	./$<

dates: dates.cpp
	$(CXX) -Wall -std=c++11 -o $@ $^

.PHONY: clean
clean:
	rm -f dates
	rm -f dates_r.txt
	rm -f dates_s.txt
	rm -f Putzplan.log
	rm -f Putzplan.aux

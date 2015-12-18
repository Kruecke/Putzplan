all: Putzplan.pdf

Putzplan.pdf: Putzplan.tex dates.txt
	pdflatex $<

dates.txt: dates
	./dates

dates: dates.cpp
	$(CXX) -Wall -std=c++11 -o $@ $^

.PHONY: clean
clean:
	rm -f dates
	rm -f dates.txt
	rm -f Putzplan.log
	rm -f Putzplan.aux

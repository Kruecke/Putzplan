CXXFLAGS := -std=c++11 -Wall
LDLIBS   := -lboost_program_options

BUILDDIR := build

.PHONY: all
all: $(BUILDDIR)/Putzplan.pdf

$(BUILDDIR)/Putzplan.pdf: Putzplan.tex $(BUILDDIR)/dates_r.txt $(BUILDDIR)/dates_s.txt
	@echo ===== Building $@ =====
	# Workaround, if user is www-data:
	#HOME=/var/www; export HOME; \
	mkdir -p $(BUILDDIR); \
	cd $(BUILDDIR); \
	pdflatex $(abspath $<)

$(BUILDDIR)/dates_r.txt $(BUILDDIR)/dates_s.txt: $(BUILDDIR)/dates $(BUILDDIR)/config.ini
	@echo ===== Running $< =====
	mkdir -p $(BUILDDIR); \
	cd $(BUILDDIR); \
	$(abspath $<)

$(BUILDDIR)/dates: dates.cpp
	@echo ===== Building $@ =====
	$(CXX) $(CXXFLAGS) -o $@ $^ $(LDLIBS)

$(BUILDDIR)/config.ini: config.ini
	@echo ===== Creating $@ =====
	cp $< $@

.PHONY: clean
clean:
	@echo ===== Cleaning $(BUILDDIR) =====
	rm -f $(BUILDDIR)/dates_*.txt
	rm -f $(BUILDDIR)/Putzplan.*

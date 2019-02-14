CXXFLAGS := -std=c++11 -Wall
LDLIBS   := -lboost_program_options

BUILDDIR ?= build

.PHONY: all
all: $(BUILDDIR)/Putzplan.pdf

$(BUILDDIR)/Putzplan.pdf: Putzplan.tex $(BUILDDIR)/dates_r.txt $(BUILDDIR)/dates_s.txt
	@echo ===== Building $@ =====
	mkdir -p $(BUILDDIR)
	# Workaround: HOME env. variable is not properly set when called by webserver.
	HOME=$(abspath $(BUILDDIR)); export HOME; \
	cd $(BUILDDIR); \
	pdflatex $(abspath $<)

$(BUILDDIR)/dates_r.txt $(BUILDDIR)/dates_s.txt: $(BUILDDIR)/dates $(BUILDDIR)/config.ini
	@echo ===== Running $< =====
	mkdir -p $(BUILDDIR)
	cd $(BUILDDIR); \
	$(abspath $<)

$(BUILDDIR)/dates: dates.cpp
	@echo ===== Building $@ =====
	mkdir -p $(BUILDDIR)
	$(CXX) $(CXXFLAGS) -o $@ $^ $(LDLIBS)

$(BUILDDIR)/config.ini: config.ini
	@echo ===== Creating $@ =====
	mkdir -p $(BUILDDIR)
	cp $< $@

.PHONY: clean
clean:
	@echo ===== Cleaning $(BUILDDIR) =====
	rm -f $(BUILDDIR)/dates_*.txt
	rm -f $(BUILDDIR)/Putzplan.*

# For development & testing
.PHONY: docker-up
docker-up:
	docker-compose up -d

.PHONY: docker-down
docker-down:
	docker-compose down

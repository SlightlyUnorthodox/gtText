# gtText testing Makefile

all:
	$(MAKE) test_front_end
	$(MAKE) test_back_end

test_front_end:
	R CMD INSTALL package/

test_back_end:
	grokit makelib package/inst/

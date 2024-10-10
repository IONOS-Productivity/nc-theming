.PHONY: build_css help

TEMPLATE_FILE=lib/Themes/CustomCss.php.tmpl
OUTPUT_FILE=lib/Themes/CustomCss.php
TEMP_CSS=css/combined.css
PLACEHOLDER=%%CSS_GOES_HERE%%

help: ## Show this help message
	@echo "Usage: make [target]"
	@echo ""
	@echo "Targets:"
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  %-20s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

build_css: ## Build the custom CSS file
	composer i
	@echo "Building CSS from files in $(CSS_DIR)..."
	cat css/*.css | sed '/^$$/d; s/^/\t/' > $(TEMP_CSS)
		cat $(TEMPLATE_FILE) > $(OUTPUT_FILE)
	sed -i "/$(PLACEHOLDER)/r $(TEMP_CSS)" $(OUTPUT_FILE)
	sed -i "/$(PLACEHOLDER)/d" $(OUTPUT_FILE)
	rm $(TEMP_CSS)
	@echo "Done."

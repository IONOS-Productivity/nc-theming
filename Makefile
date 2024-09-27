.PHONY: build_css help

OUTPUT_FILE=lib/Themes/CustomCss.php
TEMP_CSS=css/combined.css
PLACEHOLDER=\/\* Generated custom CSS - Please run make build_css \*\/
END_PLACEHOLDER=\/\* End of generated custom CSS \*\/

help: ## Show this help message
	@echo "Usage: make [target]"
	@echo ""
	@echo "Targets:"
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  %-20s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

build_css: ## Build the custom CSS file
	@echo "Building CSS from files in $(CSS_DIR)..."
	cat css/*.css | sed '/^$$/d; s/^/\t/' > $(TEMP_CSS)
	sed -i "/$(PLACEHOLDER)/,/$(END_PLACEHOLDER)/{//!d}" $(OUTPUT_FILE)
	sed -i "/$(PLACEHOLDER)/r $(TEMP_CSS)" $(OUTPUT_FILE)
	rm $(TEMP_CSS)
	@echo "Done."

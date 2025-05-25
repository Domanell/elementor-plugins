/**
 * PDF Generator for Net Sheet Calculator
 * Uses pdf-lib library for PDF generation
 */

// TODO: Recheck

const PDFGenerator = (function () {
	'use strict';

	let rgb;
	// Configuration variables for font sizes and spacing
	const config = {
		titleFontSize: 14,
		sectionTitleFontSize: 10,
		contentFontSize: 9,
		footerFontSize: 7,
		lineHeight: 16,
		sectionSpacing: 12,
		margin: 40,
	};

	// Sections in order of appearance
	const sections = [
		{ title: 'Purchase Information', fields: ['purchase_price', 'other_credits', 'gross_proceeds'] },
		{ title: 'Mortgage Payoffs', fields: ['mortgage_payoff', 'other_mortgage_payoff', 'special_assessment_payoff', 'lien_release_tracking_fee'] },
		{ title: 'Taxes', fields: ['property_taxes_due', 'michigan_transfer_tax', 'revenue_stamps'] },
		{ title: 'Title Fees', fields: ['settlement_fee', 'security_fee', 'title_insurance_policy'] },
		{ title: 'Commission Fees', fields: ['commission_realtor', 'commission_realtor_extra'] },
		{ title: 'Other Fees', fields: ['current_water', 'hoa_assessment', 'water_escrow', 'home_warranty', 'fha', 'misc_cost_seller', 'seller_attorney_fee'] },
		{ title: 'Totals', fields: ['total_closing_costs', 'estimated_net_proceeds'] },
	];

	const generatePDF = async (data, documentTitle = 'Net Sheet Calculator Results') => {
		try {
			// Use the pdf-lib library that's loaded as a dependency
			const { PDFDocument, rgb: rgbFunc, StandardFonts } = PDFLib;
			rgb = rgbFunc;
			const pdfDoc = await PDFDocument.create();
			let page = pdfDoc.addPage([595.28, 841.89]); // A4 size
			const { width, height } = page.getSize();

			const font = await pdfDoc.embedFont(StandardFonts.Helvetica);
			const boldFont = await pdfDoc.embedFont(StandardFonts.HelveticaBold);
			const margin = config.margin;
			let currentY = height - margin;

			// Using fixed sizes from config
			const lineHeight = config.lineHeight;

			// X-coordinates for labels and values
			const labelX = margin;
			const valueX = width / 2;
			const titleSize = config.titleFontSize;

			// Draw document title
			page.drawText(documentTitle, {
				x: margin,
				y: currentY,
				size: titleSize,
				font: boldFont,
				color: rgb(0, 0, 0),
			});
			currentY -= lineHeight * 1.5; // Space after title

			// Draw sections
			for (const section of sections) {
				// Check if we need a new page
				if (currentY < margin + section.fields.length * lineHeight + config.sectionSpacing) {
					page = pdfDoc.addPage([595.28, 841.89]);
					currentY = height - margin;
				}

				// Draw section title
				page.drawText(section.title, {
					x: margin,
					y: currentY,
					size: config.sectionTitleFontSize,
					font: boldFont,
					color: rgb(0, 0, 0),
				});
				currentY -= lineHeight;

				// Draw fields
				for (const field of section.fields) {
					const label = data.labels[field] || field;
					const value = data.values[field];
					const formattedValue = NSCUtils.formatCurrency(value);

					page.drawText(label + ':', {
						x: labelX + 20,
						y: currentY,
						size: config.contentFontSize,
						font: font,
						color: rgb(0, 0, 0),
					});

					page.drawText(formattedValue, {
						x: valueX,
						y: currentY,
						size: config.contentFontSize,
						font: boldFont,
						color: rgb(0, 0, 0),
					});

					currentY -= lineHeight;
				}
				currentY -= config.sectionSpacing; // Spacing between sections

				// Draw separator line except for last section
				if (section.title !== 'Totals') {
					page.drawLine({
						start: { x: margin, y: currentY + lineHeight },
						end: { x: width - margin, y: currentY + lineHeight },
						thickness: 0.5,
						color: rgb(0.7, 0.7, 0.7),
					});
					currentY -= config.sectionSpacing;
				}
			}

			const pdfBytes = await pdfDoc.save();
			return new Blob([pdfBytes], { type: 'application/pdf' });
		} catch (error) {
			console.error('Error generating PDF:', error);
			throw new Error(`PDF generation failed: ${error.message}`);
		}
	};

	const downloadPDF = async (data, filename = 'net-sheet-calculator-results.pdf') => {
		try {
			const pdfBlob = await generatePDF(data);
			const blobUrl = URL.createObjectURL(pdfBlob);
			const link = document.createElement('a');
			link.href = blobUrl;
			link.download = filename;
			link.click();

			// Clean up the blob URL after a short delay
			setTimeout(() => {
				URL.revokeObjectURL(blobUrl);
			}, 100);

			return true;
		} catch (error) {
			console.error('Error downloading PDF:', error);
			throw error;
		}
	};

	/**
	 * Convert PDF to base64 string for sending via AJAX
	 * @param {Object} data - The calculator data
	 * @returns {Promise<string>} - Base64 encoded PDF
	 */
	const getPDFAsBase64 = async (data) => {
		try {
			const pdfBlob = await generatePDF(data);

			return new Promise((resolve, reject) => {
				const reader = new FileReader();
				reader.onloadend = () => resolve(reader.result);
				reader.onerror = reject;
				reader.readAsDataURL(pdfBlob);
			});
		} catch (error) {
			console.error('Error generating PDF as base64:', error);
			throw error;
		}
	};

	return { generatePDF, downloadPDF, getPDFAsBase64 };
})();

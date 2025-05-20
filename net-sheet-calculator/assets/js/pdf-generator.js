/**
 * PDF Generator for Net Sheet Calculator
 * Uses pdf-lib library for PDF generation
 */

const PDFGenerator = (function () {
	'use strict';

	let rgb;

	// Sections in order of appearance
	const sections = [
		{ title: 'Purchase Information', fields: ['purchase_price', 'other_credits', 'gross_proceeds'] },
		{ title: 'Mortgage Payoffs', fields: ['mortgage_payoff', 'other_mortgage_payoff', 'special_assessment_payoff', 'lien_release_tracking_fee'] },
		{ title: 'Taxes', fields: ['property_taxes_due', 'michigan_transfer_tax', 'revenue_stamps'] },
		{ title: 'Title Fees', fields: ['settlement_fee', 'security_fee', 'title_insurance_policy'] },
		{ title: 'Commission Fees', fields: ['comission_realtor', 'comission_realtor_extra'] },
		{ title: 'Other Fees', fields: ['current_water', 'hoa_assessment', 'water_escrow', 'home_warranty', 'fha', 'misc_cost_seller', 'seller_attorney_fee'] },
		{ title: 'Totals', fields: ['total_closing_costs', 'estimated_net_proceeds'] },
	];

	const generatePDF = async (data, documentTitle = 'Net Sheet Calculator Results') => {
		// Use the pdf-lib library that's loaded as a dependency
		const { PDFDocument, rgb: rgbFunc, StandardFonts } = PDFLib;
		rgb = rgbFunc;
		const pdfDoc = await PDFDocument.create();
		let page = pdfDoc.addPage([612, 792]); // US Letter size
		const { width, height } = page.getSize();

		const font = await pdfDoc.embedFont(StandardFonts.Helvetica);
		const boldFont = await pdfDoc.embedFont(StandardFonts.HelveticaBold);
		// Count total items for auto-scaling
		const totalItems = sections.reduce((sum, section) => sum + section.fields.length, 0);

		// Page margins (40px from all sides)
		const margin = 40;
		let currentY = height - margin;

		// Calculate available vertical space
		const availableHeight = height - margin * 2;

		// Minimum line height for readability
		const minLineHeight = 10;

		// Auto-scale line height based on content amount
		const lineHeight = Math.max(minLineHeight, Math.floor(availableHeight / (totalItems + sections.length * 2)));

		// X-coordinates for labels and values
		const labelX = margin;
		const valueX = width / 2; // Title font size (scales between 12-16pt based on available space)
		const titleSize = Math.min(16, Math.max(12, lineHeight * 1.2));
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
			// Draw section title
			page.drawText(section.title, {
				x: margin,
				y: currentY,
				size: 11, // Slightly smaller font
				font: boldFont,
				color: rgb(0, 0, 0),
			});
			currentY -= lineHeight; // Draw fields
			for (const field of section.fields) {
				const label = data.labels[field] || field;
				const value = data.values[field];
				const formattedValue =
					typeof value === 'number' ? `$${value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}` : String(value || '');

				page.drawText(label + ':', {
					x: labelX + 20,
					y: currentY,
					size: 9, // Smaller font size
					font: boldFont,
					color: rgb(0, 0, 0),
				});

				page.drawText(formattedValue, {
					x: valueX,
					y: currentY,
					size: 9, // Smaller font size
					font: font,
					color: rgb(0, 0, 0),
				});

				currentY -= lineHeight;
			}
			currentY -= lineHeight * 0.5; // Spacing between sections

			// Draw separator line except for last section
			if (section.title !== 'Totals') {
				page.drawLine({
					start: { x: margin, y: currentY + lineHeight },
					end: { x: width - margin, y: currentY + lineHeight },
					thickness: 0.5,
					color: rgb(0.7, 0.7, 0.7), // Light gray color
				});
				// Add more space after the separator line
				currentY -= lineHeight * 0.5;
			}
		}

		// Add generation date at the bottom
		page.drawText(`Generated on: ${new Date().toLocaleDateString()}`, {
			x: margin,
			y: margin / 2,
			size: 8,
			font: font,
			color: rgb(0.5, 0.5, 0.5),
		});

		const pdfBytes = await pdfDoc.save();
		return new Blob([pdfBytes], { type: 'application/pdf' });
	};

	const downloadPDF = async (data, filename = 'net-sheet-calculator-results.pdf') => {
		try {
			const pdfBlob = await generatePDF(data);
			const blobUrl = URL.createObjectURL(pdfBlob);
			const link = document.createElement('a');
			link.href = blobUrl;
			link.download = filename;
			link.click();

			setTimeout(() => {
				URL.revokeObjectURL(blobUrl);
			}, 100);

			return true;
		} catch (error) {
			console.error('Error generating PDF:', error);
			return false;
		}
	};

	return { generatePDF, downloadPDF };
})();

if (typeof module !== 'undefined' && module.exports) {
	module.exports = PDFGenerator;
}

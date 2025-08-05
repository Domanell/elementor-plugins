/**
 * PDF Generator for Real Estate Closing Calculator
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

	const generatePDF = async (data, config) => {
		try {
			const { documentTitle, filename, sections } = config;
			const { labels, values, companyInfo } = data;
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
			const lineHeight = config.lineHeight; // X-coordinates for labels and values
			const labelX = margin;
			const valueX = width / 2;
			const titleSize = config.titleFontSize;

			// Add logo if available
			if (companyInfo.logo && companyInfo.logo.url) {
				try {
					// Fetch logo image
					const url = companyInfo.logo.url;
					const logoResponse = await fetch(url);
					const logoArrayBuffer = await logoResponse.arrayBuffer();

					let logoImage;
					// Check image format based on URL
					const isJpg = url.toLowerCase().endsWith('.jpg') || url.toLowerCase().endsWith('.jpeg');

					if (isJpg) {
						logoImage = await pdfDoc.embedJpg(logoArrayBuffer);
					} else {
						// Default to PNG
						logoImage = await pdfDoc.embedPng(logoArrayBuffer);
					}

					// Calculate logo dimensions while preserving aspect ratio
					const logoWidth = companyInfo.logo.width || 100;
					const logoHeight = companyInfo.logo.height || 52;

					const logoY = height - margin - logoHeight + 6;

					// Draw the logo at top left
					page.drawImage(logoImage, {
						x: margin,
						y: logoY,
						width: logoWidth,
						height: logoHeight,
					});

					// Draw address lines to the right of the logo
					const addressLines = [companyInfo.address1, companyInfo.address2, companyInfo.phone];
					const addressX = margin + logoWidth + 220; // 20px padding between logo and address

					// Move address lines down slightly (e.g., 10px lower)
					const addressYOffset = 10;
					const addressY = logoY + logoHeight - config.contentFontSize - addressYOffset;

					for (let i = 0; i < addressLines.length; i++) {
						page.drawText(addressLines[i], {
							x: addressX,
							y: addressY - i * 12,
							size: config.contentFontSize,
							font: font,
							color: rgb(0, 0, 0),
						});
					}

					// Update currentY to go below both logo and address
					const addressBlockHeight = addressLines.length * 12;
					const contentTop = Math.max(logoHeight, addressBlockHeight);
					currentY = height - margin - contentTop - 20;
				} catch (logoError) {
					console.error('Error embedding logo:', logoError);
				}
			}

			// Draw a single separator line above the title
			page.drawLine({
				start: { x: margin, y: currentY + lineHeight },
				end: { x: width - margin, y: currentY + lineHeight },
				thickness: 1,
				color: rgb(0.7, 0.7, 0.7),
			});

			currentY -= 10;

			// Draw centered document title
			const titleWidth = boldFont.widthOfTextAtSize(documentTitle, titleSize);
			const titleX = (width - titleWidth) / 2;

			page.drawText(documentTitle, {
				x: titleX,
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
					const label = labels[field] || field;
					const value = values[field];

					const formattedValue =
						field === 'commission_realtor'
							? `${RECCUtils.formatCurrency(values.commission_realtor_amount)} (${RECCUtils.formatPercentage(value)})`
							: RECCUtils.formatCurrency(value);

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
				// if (section.title !== 'Totals') {
				// 	page.drawLine({
				// 		start: { x: margin, y: currentY + lineHeight },
				// 		end: { x: width - margin, y: currentY + lineHeight },
				// 		thickness: 0.5,
				// 		color: rgb(0.7, 0.7, 0.7),
				// 	});
				// 	currentY -= config.sectionSpacing;
				// }
			}

			const pdfBytes = await pdfDoc.save();
			return new Blob([pdfBytes], { type: 'application/pdf' });
		} catch (error) {
			console.error('Error generating PDF:', error);
			throw new Error(`PDF generation failed: ${error.message}`);
		}
	};

	const downloadPDF = async (data, config) => {
		try {
			const pdfBlob = await generatePDF(data, config);
			const blobUrl = URL.createObjectURL(pdfBlob);
			const link = document.createElement('a');
			link.href = blobUrl;
			link.download = config.filename || 'calcualtor-result.pdf';
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
	 * @param {Object} config - PDF configuration
	 * @param {string} config.documentTitle - Title of the PDF document
	 * @param {string} config.filename - Name of the PDF file
	 * @param {Array} config.sections - Sections to include in the PDF
	 * @returns {Promise<string>} - Base64 encoded PDF
	 */
	const getPDFAsBase64 = async (data, config) => {
		try {
			const pdfBlob = await generatePDF(data, config);

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

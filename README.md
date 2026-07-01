# ComPDF Conversion SDK for PHP

As part of the KDAN ecosystem, ComPDF Conversion SDK brings PDF document conversion to your PHP web applications. Convert PDF files into editable Office documents, images, HTML, and more — directly from your PHP backend. Whether you're building a document management system, an online converter service, or an enterprise web portal, you can integrate PDF conversion that preserves layout, tables, and formatting.

> If you find this library helpful, please consider giving us a ⭐ **Star** on GitHub! Have feedback or questions? Join the conversation in our [Discussions](https://github.com/ComPDFKit/compdfkit-conversion-sdk-php/discussions).

**Supported Features**

* Convert PDF to Word (.docx)

* Convert PDF to Excel (.xlsx)

* Convert PDF to PowerPoint (.pptx)

* Convert PDF to HTML (.html)

* Convert PDF to Image (.png, .jpg, .jpeg, .jpeg2000, .bmp, .tiff, .tga, .gif, .webp)

* Convert PDF to Plain Text (.txt)

* Convert PDF to Rich Text Format (.rtf)

* Convert PDF to Searchable PDF (.pdf)

* Convert PDF to Structured Data (.json)

* Convert PDF to Markdown (.md)

* Convert PDF to OFD (.ofd)

* Optical Character Recognition (OCR)

* Layout Analysis

* Table Recognition

## Table of Contents

* [Why ComPDF Conversion SDK](#why-compdf-conversion-sdk)
* [Conversion Quality Preview](#conversion-quality-preview)
* [Requirements](#requirements)
* [How to Run a Demo](#how-to-run-a-demo)
* [How to Integrate the SDK](#how-to-integrate-the-sdk)
* [License and Free Trial](#license-and-free-trial)
* [Packaging and Delivery](#packaging-and-delivery)
* [Developer Guides for All Features](#developer-guides-for-all-features)
* [API Reference](#api-reference)
* [FAQ](#faq)
* [Changelog](#changelog)
* [Technical Support](#technical-support)

## Why ComPDF Conversion SDK

- **Mature Technology:** With years of technology accumulation, we have established a complete mechanism of product iteration to offer a continuous guarantee for product competitiveness.
- **High-Fidelity Conversion:** Designed to preserve layout, tables, text structure, and formatting in the output files.
- **AI-Enhanced Conversion:** Continuously improved for complex layout analysis, table recognition, OCR, and editable output.
- **Independent Intellectual Property Rights:** Our technology is independent and compliant with ISO, helping enterprises conduct international business without considering copyright risks.
- **High-quality Service:** We provide 24/7 professional one-to-one technical support, including onsite service and remote assistance via phone and email.

## Conversion Quality Preview

With **AI table recognition, AI layout analysis, and OCR capabilities**, ComPDF Conversion SDK delivers accurate PDF conversion results. Simply select the appropriate conversion parameters based on your document type to achieve accurate document reconstruction. Below, we demonstrate the power of the ComPDF Conversion SDK by converting PDFs to Word, Excel, or searchable PDF files. Contact us to get a demo and experience our product's performance firsthand.

![conversion sdk performance GIF](./Images/conversion%20sdk%20performance%20GIF.gif)

### Convert PDF to Word

ComPDF Conversion SDK accurately restores all content and images, complex structures, paragraph aggregation, natural reading order, text styles, bold formatting, fonts, multi‑column layouts, image-text positioning, ordered lists, and more. Get a fully editable Word document that looks exactly like the original PDF.

![conversion sdk performance GIF](./Images/Performance%20-%20PDF%20to%20Word.png)

### Convert PDF to Excel

Extract standard tables and borderless tables with precision, preserve the content and images inside table cells, and retain text styles such as bold and color. ComPDF Conversion SDK turns PDF tables into fully functional Excel spreadsheets without losing structure or formatting.

![Conversion SDK: PDF to Excel](./Images/Performance%20-%20PDF%20to%20Excel.png)

### Convert Scanned PDF to editable text

Convert scanned documents and scanned PDFs into searchable PDFs while perfectly preserving the original layout and formatting. Every word becomes searchable, locatable, and selectable – ready for copying just like native digital text.

![conversion sdk performance GIF](./Images/Performance%20-%20PDF%20to%20searchable%20PDF.png)

## Requirements

**PHP 7.4+ (8.x recommended), ext-ffi.** Cross-platform (Windows/Linux/macOS).

| Platform | System Requirements       | Development Environment                                     | Note                                                                                            |
| -------- | ------------------------- | ----------------------------------------------------------- | ----------------------------------------------------------------------------------------------- |
| Windows  | Windows 10 or later, x64. | PHP 7.4+, PHP 8.x recommended; Composer; `ext-ffi` enabled. | The PHP architecture must match the native DLL architecture, for example both x64.              |
| Linux    | Linux x86_64.             | PHP 7.4+, PHP 8.x recommended; Composer; `ext-ffi` enabled. | Tested on Ubuntu 20.04 / WSL x86_64. Conversion scripts should be started with `bin/compdfphp`. |
| macOS    | macOS 10.14 or later.     | PHP 7.4+; Composer; `ext-ffi` enabled.                      | The PHP SDK reserves the macOS directory layout. Validate before delivery.                      |

PHP FFI must be enabled in `php.ini`:

```ini
extension=ffi
ffi.enable=true
```

## How to Run a Demo

The PHP SDK provides demos in the ***"samples"*** folder. Run `php demo.php` from the samples directory to get started quickly.

### Linux

```bash
cd path/to/php
composer install --no-dev -o
bin/compdfphp samples/version.php
bin/compdfphp samples/direct_convert_demo.php
```

Or use the Composer script:

```bash
composer run demo:convert
```

### Windows

```bat
cd path\to\php
composer install --no-dev -o
php -m | findstr /i FFI
set COMPDFKIT_DEMO_LICENSE=LICENSE_KEY
set COMPDFKIT_DEMO_APP_ID=com.your.app
bin\compdfphp.bat samples\direct_convert_demo.php
```

## How to Integrate the SDK

### Install via Composer

Navigate to the PHP SDK package directory and run:

```bash
composer install
```

### Verify License and Initialize

Before using ComPDF Conversion SDK classes and methods, verify the license and initialize the SDK.

```php
use ComPDFKit\Conversion\LibraryManager;
use ComPDFKit\Conversion\ErrorCode;

$license = 'LICENSE_KEY';
$deviceId = '';
$appId = 'com.your.app';

$code = LibraryManager::licenseVerify($license, $deviceId, $appId);
if (!ErrorCode::isSuccess($code)) {
    throw new RuntimeException('License verify failed: ' . ErrorCode::describe($code));
}

LibraryManager::initialize(__DIR__);
```

## License and Free Trial

### Get Free Trial License

[Contact our sales team](https://www.compdf.com/contact-sales) and we'll send you a 30-day free trial license for ComPDF Conversion SDK.

### Get Commercial License

ComPDF Conversion SDK is a commercial SDK that requires a license for application release. Any documents, sample code, or source code distribution from the released package of ComPDF to any third party is prohibited. To get commercial license for ComPDF Conversion SDK, feel free to [contact our sales team](https://www.compdf.com/contact-sales?utm_source=github&utm_medium=compdfkit-conversion-sdk-php&utm_campaign=compdfkit_conversion_sdk_php_repo&ref_platform_id=github_compdfkit).

For PHP Conversion SDK, the commercial license must match the target platform and application information. For example, a Windows runtime requires a Windows-compatible license, and a Linux runtime requires a Linux-compatible license.

### Apply the License Key

If you haven't got a license key, please check out [how to obtain a license key](#license-and-free-trial). ComPDF Conversion SDK currently supports offline authentication to verify license keys.

*Learn more:* [*What is the authentication mechanism of ComPDF's license?*](https://www.compdf.com/faq/authentication-mechanism-of-compdfkit-license?utm_source=github&utm_medium=compdfkit-conversion-sdk-php&utm_campaign=compdfkit_conversion_sdk_php_repo&ref_platform_id=github_compdfkit)

**Copy the License Key:**

Accurately obtaining the license key is crucial for the application of the license.

1. In the email you received, locate the XML file containing the license key.

2. Open the XML file, and determine the license type based on the `<type>` field. If `<type>online</type>` is present, it indicates an online license. If `<type>offline</type>` is present or if the field is absent, it indicates an offline license.
- **Online License:**
  
      <?xml version="1.0" encoding="UTF-8" standalone="no"?>
      <license version="1">
          <platform>windows</platform>
          <starttime>xxxxxxxx</starttime>
          <endtime>xxxxxxxx</endtime>
          <type>online</type>
          <key>LICENSE_KEY</key>
      </license>

- **Offline License:**
  
  ```xml
  <?xml version="1.0" encoding="UTF-8" standalone="no"?>
  <license version="1">
      <platform>linux</platform>
      <starttime>xxxxxxxx</starttime>
      <endtime>xxxxxxxx</endtime>
      <key>LICENSE_KEY</key>
  </license>
  ```
3. Copy the value located at the LICENSE_KEY position within the `<key>LICENSE_KEY</key>` field. This is your license key.

**Apply the License Key**

You can perform offline authentication using the following method:

Before using the classes and methods of the ComPDF Conversion SDK in your project, you need to initialize the SDK with a valid license key. If you don't have a license key, feel free to contact the [ComPDF team](https://www.compdf.com/contact-sales?utm_source=github&utm_medium=compdfkit-conversion-sdk-php&utm_campaign=compdfkit_conversion_sdk_php_repo&ref_platform_id=github_compdfkit) to obtain one.

```php
use ComPDFKit\Conversion\LibraryManager;
$code = LibraryManager::licenseVerify($license, $deviceId, $appId);
LibraryManager::initialize(__DIR__);
```

## Packaging and Delivery

### Linux Package

The Linux package should include PHP source code, Composer configuration, launcher scripts, and native `.so` files. Prepare native libraries and validate the package:

```bash
cd convertion/compdf_sdk/php
composer install
composer run prepare-libs
composer run demo:convert
composer test
```

Use the following launcher when running actual conversion scripts on Linux:

```bash
bin/compdfphp your_script.php
```

### Windows Package

Put the Windows DLLs from the same native SDK build into:

```text
lib/windows/x64/
```

Then package on Linux or macOS:

```bash
composer run package:windows
```

The generated zip file is located at:

```text
dist/compdfkit-conversion-php-sdk-windows.zip
```

Copy the zip file to Windows, unzip it, and run:

```bat
composer install --no-dev -o
set COMPDFKIT_DEMO_LICENSE=LICENSE_KEY
set COMPDFKIT_DEMO_APP_ID=com.your.app
bin\compdfphp.bat samples\direct_convert_demo.php
```

## Developer Guides for All Features

ComPDF Conversion SDK for PHP provides a wide range of customizable conversion options, such as whether to include images or annotations in PDF documents during file conversion, enable OCR, perform layout analysis, and more. Explore the [guides](https://www.compdf.com/guides/conversion-sdk/php/overview?utm_source=github&utm_medium=compdfkit-conversion-sdk-php&utm_campaign=compdfkit_conversion_sdk_php_repo&ref_platform_id=github_compdfkit) for sample code and learn how to customize conversions for the following features.

* Initialize Library Resources
* Get SDK Version and Remaining Quota
* PDF to Word
* PDF to Word, Excel, PPT, RTF, HTML, images, searchable PDF, TXT, JSON, markdown, and OFD. 
* Select Page Range for Conversion
* Conversion Options: Contain Image & Annotation
* Page Layout Mode
* OCR
* Convert Images to Other Document Formats
* Layout Analysis and Table Recognition
* Image Output Options
* Excel Output Options
* HTML Output Options

## API Reference

### LibraryManager

`LibraryManager` manages SDK lifecycle, license verification, initialization, and version information.

Common methods:

| Method                                       | Description                     |
| -------------------------------------------- | ------------------------------- |
| `licenseVerify($license, $deviceId, $appId)` | Verify the license.             |
| `initialize($resourcePath)`                  | Initialize the SDK.             |
| `release()`                                  | Release the SDK.                |
| `setLogger($enableInfo, $enableWarning)`     | Configure native logger output. |
| `getPageCount($filePath, $password)`         | Get PDF page count.             |
| `getRemainingPageQuota()`                    | Get remaining page quota.       |
| `getVersion()`                               | Get SDK version.                |

### ConvertOption

`ConvertOption` configures conversion parameters. Common properties:

| Property                   | Description                                        |
| -------------------------- | -------------------------------------------------- |
| `containImage`             | Whether to include images.                         |
| `containAnnotation`        | Whether to include annotations.                    |
| `enableOcr`                | Whether to enable OCR.                             |
| `languages`                | OCR language array.                                |
| `ocrOption`                | OCR recognition mode.                              |
| `enableAiLayout`           | Whether to enable layout analysis.                 |
| `enableAiTableRecognition` | Whether to enable table recognition.               |
| `pageLayoutMode`           | Page layout mode.                                  |
| `pageRanges`               | Page ranges, such as `1-3,5`.                      |
| `outputDocumentPerPage`    | Whether to output one file per page.               |
| `autoCreateFolder`         | Whether to create the output folder automatically. |
| `imageType`                | Image output format.                               |
| `imageColorMode`           | Image color mode.                                  |
| `imageScaling`             | Image scaling ratio.                               |
| `excelWorksheetOption`     | Excel worksheet output mode.                       |
| `htmlOption`               | HTML output mode.                                  |

### ErrorCode

`ErrorCode` checks and describes native SDK error codes.

```php
$code = Conversion::pdfToWord('input.pdf', '', 'output.docx', new ConvertOption());
if (!ErrorCode::isSuccess($code)) {
    echo ErrorCode::describe($code) . PHP_EOL;
}
```

## FAQ

**1. Composer Is Not Found**

If `composer install` reports that `composer` is not recognized, Composer is not installed on Windows or has not been added to `PATH`. Install Composer for Windows and reopen the command line.

**2. PHP FFI Is Not Enabled**

If PHP reports that the FFI extension is missing, check `php.ini`:

```ini
extension=ffi
ffi.enable=true
```

Then reopen the command line and run:

```bash
php -m
```

Make sure the output contains `FFI`.

**3. Windows Cannot Find cpdfconversionsdk.dll**

Make sure the DLL is located at:

```text
lib/windows/x64/cpdfconversionsdk.dll
```

Start scripts with `bin\compdfphp.bat`, or set the path manually:

```bat
set COMPDFKIT_LIB_DIR=%CD%\lib\windows\x64
set PATH=%CD%\lib\windows\x64;%PATH%
```

**4. Windows Reports an ONNX Runtime API Version Mismatch**

If an error similar to the following appears:

```text
The requested API version [18] is not available, only API versions [1, 17] are supported in this build.
```

The loaded `onnxruntime.dll` does not match `cpdfconversionsdk.dll` or `DocumentAI.dll`. Make sure all DLLs in `lib/windows/x64` come from the same SDK build.

**5. License Invalid**

If `License invalid` is returned, check the following:

- Whether the license key is copied completely.
- Whether the license platform matches the current runtime platform.
- Whether `COMPDFKIT_APP_ID` or `COMPDFKIT_DEMO_APP_ID` is the real App ID bound to the license.
- Whether a license file path was passed as the license key by mistake. Follow the license instructions and sample requirements.

**6. PHP Script Crashes When Run Directly on Linux**

Do not run the following directly on Linux:

```bash
php samples/direct_convert_demo.php
```

Use:

```bash
bin/compdfphp samples/direct_convert_demo.php
```

`bin/compdfphp` preloads native dependencies before PHP starts, avoiding native SDK loading-order issues.

* [More FAQ](https://www.compdf.com/faq?utm_source=github&utm_medium=compdfkit-conversion-sdk-php&utm_campaign=compdfkit_conversion_sdk_php_repo&ref_platform_id=github_compdfkit)

## Changelog

Go to our [changelog](https://www.compdf.com/conversion-sdk/changelog-php?utm_source=github&utm_medium=compdfkit-conversion-sdk-php&utm_campaign=compdfkit_conversion_sdk_php_repo&ref_platform_id=github_compdfkit) to keep up with the latest updates, improvements, and bug fixes.

## Technical Support

Thanks for your interest in ComPDF Conversion SDK, the easy-to-use and powerful development solution. If you encounter technical questions or bug issues when using ComPDF Conversion SDK, please submit the problem report to the [ComPDF team](mailto:support@compdf.com). More information as follows would help us to solve your problem:

- ComPDF Conversion SDK product and version.
- Your operating system and IDE version.
- Detailed descriptions of the problem.
- Any other related information, such as an error screenshot.

Home link: [https://www.compdf.com](https://www.compdf.com/?utm_source=github&utm_medium=compdfkit-conversion-sdk-php&utm_campaign=compdfkit_conversion_sdk_php_repo&ref_platform_id=github_compdfkit)

Technical Support: [https://www.compdf.com/support](https://www.compdf.com/support?utm_source=github&utm_medium=compdfkit-conversion-sdk-php&utm_campaign=compdfkit_conversion_sdk_php_repo&ref_platform_id=github_compdfkit)

Email: [support@compdf.com](mailto:support@compdf.com)

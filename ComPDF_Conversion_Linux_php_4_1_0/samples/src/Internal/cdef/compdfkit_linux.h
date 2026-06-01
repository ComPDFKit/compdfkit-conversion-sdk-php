// ComPDFKit Conversion SDK — FFI cdef (Linux / macOS)
//
// This file is consumed by PHP FFI::cdef(). It MUST NOT contain preprocessor
// directives other than FFI_* ones, calling-convention macros, attributes,
// or extern "C" blocks. Keep this file aligned with python/include/*_c.h.

#define FFI_SCOPE "COMPDFKIT_CONVERSION"
#define FFI_LIB "libcompdf_php_shim.so"

typedef _Bool bool;

// ---------------------------------------------------------------------------
// Enums (compdf_basictypes_c.h)
// ---------------------------------------------------------------------------

typedef enum {
    e_CErrSuccess = 0,
    e_CErrCancel = 1,
    e_CErrFile = 2,
    e_CErrPDFPassword = 3,
    e_CErrPDFPage = 4,
    e_CErrPDFFormat = 5,
    e_CErrPDFSecurity = 6,
    e_CErrOutOfMemory = 7,
    e_CErrIO = 8,
    e_CErrCompress = 9,
    e_CErrLicenseInvalid = 20,
    e_CErrLicenseExpire = 21,
    e_CErrLicenseUnsupportedPlatform = 22,
    e_CErrLicenseUnsupportedID = 23,
    e_CErrLicenseUnsupportedDevice = 24,
    e_CErrLicensePermissionDeny = 25,
    e_CErrLicenseUninitialized = 26,
    e_CErrLicenseIllegalAccess = 27,
    e_CErrLicenseFileReadFailed = 28,
    e_CErrLicenseOCRPermissionDeny = 29,
    e_CErrNoTable = 40,
    e_CErrOCRFailure = 41,
    e_CErrConverting = 60,
    e_CErrInvalidArg = 80,
    e_CErrInvalidHandle = 81,
    e_CErrModelInvalidFormat = 82,
    e_CErrModelFunctionUnsupported = 83,
    e_CErrModelFormatUnsupported = 84,
    e_CErrModelSDKMismatch = 85,
    e_CErrImageDataEmpty = 86,
    e_CErrImageWHError = 87,
    e_CErrImageUnsupportedFormat = 88,
    e_CErrImageInvalid = 89,
    e_CErrExpire = 90,
    e_CErrMissingArg = 91,
    e_CErrLicenseUnsupportedAPI = 92,
    e_CErrLicenseMismatch = 93,
    e_CErrInvalidTable = 94,
    e_CErrUnknown = 100
} CSDKErrorCode;

typedef enum {
    e_CUNKNOWN = 0,
    e_CCHINESE,
    e_CCHINESE_TRA,
    e_CENGLISH,
    e_CKOREAN,
    e_CJAPANESE,
    e_CLATIN,
    e_CDEVANAGARI,
    e_CCYRILLIC,
    e_CARABIC,
    e_CTAMIL,
    e_CTELUGU,
    e_CKANNADA,
    e_CTHAI,
    e_CGREEK,
    e_CEslav,
    e_CAUTO
} COCRLanguage;

typedef enum { e_PageLayoutModeBox, e_PageLayoutModeFlow } CPageLayoutMode;

typedef enum {
    e_CInvalidCharacter,
    e_CScanPage,
    e_CInvalidCharacterAndScanPage,
    e_CAll
} COCROption;

typedef enum { e_CColor, e_CGray, e_CBinary } CImageColorMode;

typedef enum {
    e_CJPG, e_CJPEG, e_CJPEG2000, e_CPNG, e_CBMP,
    e_CTIFF, e_CTGA, e_CGIF, e_CWEBP
} CImageType;

typedef enum {
    e_CForTable, e_CForPage, e_CForDocument
} CExcelWorksheetOption;

typedef enum {
    e_CSinglePage,
    e_CSinglePageWithBookmark,
    e_CMultiPage,
    e_CMultiPageWithBookmark
} CHtmlOption;

// ---------------------------------------------------------------------------
// CConvertOption (must match compdf_basictypes_c.h binary layout exactly)
// ---------------------------------------------------------------------------

typedef struct CConvertOption {
    bool enable_ai_layout;
    bool enable_ai_table_recognition;
    bool contain_image;
    bool contain_page_background_image;
    bool json_contain_table;
    bool contain_annotation;
    bool excel_all_content;
    bool excel_csv_format;
    bool enable_ocr;
    bool transparent_text;
    bool txt_table_format;
    bool image_path_enhance;
    bool formula_to_image;
    bool auto_create_folder;
    bool output_document_per_page;
    int  language_count;
    float image_scaling;
    int  page_layout_mode;          // CPageLayoutMode
    int  excel_worksheet_option;    // CExcelWorksheetOption
    int  html_option;               // CHtmlOption
    int  ocr_option;                // COCROption
    int  image_color_mode;          // CImageColorMode
    int  image_type;                // CImageType
    char font_name[256];
    char page_ranges[256];
    int* languages;                 // COCRLanguage*
} CConvertOption;

// ---------------------------------------------------------------------------
// Conversion callbacks (compdf_basictypes_c.h / com_config_macros.h).
// Each function pointer can be NULL; the SDK then falls back to its built-in
// behaviour (no progress, no cancel, built-in DocumentAI model).
// ---------------------------------------------------------------------------

typedef void  (*CProgress)(int current_page, int total_page);
typedef bool  (*CCancel)(void);
typedef bool  (*COCRCallback)(const char* image_path);
typedef bool  (*CLayoutCallback)(const char* image_path);
typedef bool  (*CTableCallback)(const char* image_path);
typedef const char* (*CGetOCRResultCallback)(void);
typedef const char* (*CGetLayoutResultCallback)(void);
typedef const char* (*CGetTableResultCallback)(void);

typedef struct CConvertCallback {
    void*                    handle;
    CCancel                  cancel;
    CProgress                progress;
    COCRCallback             ocr;
    CLayoutCallback          layout;
    CTableCallback           table;
    CGetOCRResultCallback    get_ocr_result;
    CGetLayoutResultCallback get_layout_result;
    CGetTableResultCallback  get_table_result;
} CConvertCallback;

// ---------------------------------------------------------------------------
// Document AI types (sdk_document_ai_common.h)
// ---------------------------------------------------------------------------

typedef int errcode;

typedef enum {
    DA_PIX_FMT_UNKNOWN,
    DA_PIX_FMT_GRAY8,
    DA_PIX_FMT_BGR565,
    DA_PIX_FMT_BGR888,
    DA_PIX_FMT_BGRA8888
} da_pixel_format_e;

typedef struct {
    void* data;
    int   pixel_format;
    int   width;
    int   height;
} da_image_t;

typedef struct { float x; float y; } da_pointf_t;
typedef struct { int x; int y; } da_point_t;
typedef struct { int start_x; int start_y; int end_x; int end_y; } da_line_t;
typedef struct { int left; int top; int right; int bottom; } da_rect_t;

typedef struct {
    char     object[128];
    da_rect_t position;
    float    confidence;
} da_detection_t;

typedef struct {
    char     text[64];
    da_rect_t bbox;
} da_ocr_word_t;

typedef struct {
    char     text[512];
    float    confidence;
    float    font_size;
    int      type;
    int      text_color_r;
    int      text_color_g;
    int      text_color_b;
    int      word_count;
    da_ocr_word_t* words;
} da_ocr_rec_t;

typedef struct { int bbox[8]; } da_ocr_det_t;

typedef struct {
    float    score;
    int      text_length;
    char*    text;
    da_rect_t bbox;
} da_text_line_t;

typedef struct {
    int      start_row;
    int      end_row;
    int      start_col;
    int      end_col;
    int      text_length;
    int      line_count;
    int      cell_background_color_r;
    int      cell_background_color_g;
    int      cell_background_color_b;
    char*    text;
    da_text_line_t* lines;
    da_rect_t bbox;
} da_table_cell_t;

typedef struct {
    int      num;
    int      row_count;
    int      col_count;
    int      cell_count;
    int      horizon_lines_count;
    int      vertical_lines_count;
    float    angle;
    float    confidence;
    char     table_type[32];
    char*    json_str;
    char*    html_str;
    int*     height_of_rows;
    int*     width_of_cols;
    da_line_t* horizon_lines;
    da_line_t* vertical_lines;
    da_table_cell_t* table_cells;
    da_rect_t bound;
} da_table_t;

typedef struct {
    da_point_t top_left;
    da_point_t top_right;
    da_point_t bottom_left;
    da_point_t bottom_right;
    da_image_t image;
} da_dewarp_t;

// ---------------------------------------------------------------------------
// Common library (LibraryManager equivalents — exported in libcpdfconversionsdk)
// ---------------------------------------------------------------------------

CSDKErrorCode CPDF_LicenseVerify(const char* license, const char* device_id, const char* app_id);
void          CPDF_Initialize(const char* resource_path);
void          CPDF_SetLogger(bool enable_info, bool enable_warning);
int           CPDF_GetPageCount(const char* file_path, const char* password);
int           CPDF_GetRemainingPageQuota(void);
void          CPDF_GetVersion(char* version);
void          CPDF_Release(void);

CSDKErrorCode CPDF_SetDocumentAIModel(const char* model_path, int gpu_id);
void          CPDF_ReleaseDocumentAIModel(void);
void          CPDF_SetDocumentAIModelCount(int layout_model_count, int table_model_count);

// ---------------------------------------------------------------------------
// Conversion API (compdf_conversion_c.h)
// ---------------------------------------------------------------------------

CSDKErrorCode CPDF_StartPDFToWord(const char* file_path, const char* password, const char* output_path, const CConvertOption* options, CConvertCallback* callback);
CSDKErrorCode CPDF_StartPDFToRtf(const char* file_path, const char* password, const char* output_path, const CConvertOption* options, CConvertCallback* callback);
CSDKErrorCode CPDF_StartPDFToExcel(const char* file_path, const char* password, const char* output_path, const CConvertOption* options, CConvertCallback* callback);
CSDKErrorCode CPDF_StartPDFToPpt(const char* file_path, const char* password, const char* output_path, const CConvertOption* options, CConvertCallback* callback);
CSDKErrorCode CPDF_StartPDFToHtml(const char* file_path, const char* password, const char* output_path, const CConvertOption* options, CConvertCallback* callback);
CSDKErrorCode CPDF_StartPDFToImage(const char* file_path, const char* password, const char* output_path, const CConvertOption* options, CConvertCallback* callback);
CSDKErrorCode CPDF_StartPDFToSearchablePDF(const char* file_path, const char* password, const char* output_path, const CConvertOption* options, CConvertCallback* callback);
CSDKErrorCode CPDF_StartPDFToTxt(const char* file_path, const char* password, const char* output_path, const CConvertOption* options, CConvertCallback* callback);
CSDKErrorCode CPDF_StartPDFToJson(const char* file_path, const char* password, const char* output_path, const CConvertOption* options, CConvertCallback* callback);
CSDKErrorCode CPDF_StartPDFToMarkdown(const char* file_path, const char* password, const char* output_path, const CConvertOption* options, CConvertCallback* callback);
CSDKErrorCode CPDF_StartPDFToOfd(const char* file_path, const char* password, const char* output_path, const CConvertOption* options, CConvertCallback* callback);

// ---------------------------------------------------------------------------
// Document AI API (compdf_document_ai_c.h)
// ---------------------------------------------------------------------------

CSDKErrorCode CPDF_Ocr(const char* file_path, int* languages, int language_count,
                       da_ocr_det_t** det_result, da_ocr_rec_t** rec_result, int* result_count);
CSDKErrorCode CPDF_LayoutAnalysis(const char* file_path, da_detection_t** detection_result, int* detection_count);
CSDKErrorCode CPDF_StampDetection(const char* file_path, da_detection_t** detection_result, int* detection_count);
CSDKErrorCode CPDF_TableRec(const char* file_path, da_table_t** table_result, int* table_count);
CSDKErrorCode CPDF_MagicColor(const char* file_path, const char* output_path);
CSDKErrorCode CPDF_Dewarp(const char* file_path, da_dewarp_t* doc, const char* output_path);

void CPDF_OcrRelease(da_ocr_det_t* det_result, da_ocr_rec_t* rec_result);
void CPDF_LayoutAnalysisRelease(da_detection_t* detection_result);
void CPDF_TableRecRelease(da_table_t** table_result, int table_count);
void CPDF_DewarpRelease(da_dewarp_t* doc);
void CPDF_StampDetectionRelease(da_detection_t* detection_result);

#include "defered.h"

#include "ddtrace_string.h"
#include "dispatch.h"

void dd_load_defered_integration_list(ddtrace_defered_integration *list, size_t size) {
    for (size_t i = 0; i < size; ++i) {
        ddtrace_defered_integration integration = list[i];
        zval class_name;
        zval function_name;
        zval loader;
#if PHP_VERSION_ID >= 70000
        if (integration.class_name.ptr) {
            ZVAL_STRINGL(&class_name, integration.class_name.ptr, integration.class_name.len);
        } else {
            ZVAL_NULL(&class_name);
        }
        ZVAL_STRINGL(&function_name, integration.fname.ptr, integration.fname.len);
        ZVAL_STRINGL(&loader, integration.loader.ptr, integration.loader.len);

        ddtrace_defered_load_via_function(class_name, function_name, loader);

        zval_dtor(&function_name);
        zval_dtor(&class_name);
        zval_dtor(&loader);
#else
        if (integration.class_name.ptr) {
            ZVAL_STRINGL(&class_name, integration.class_name.ptr, integration.class_name.len, 0);
        } else {
            ZVAL_NULL(&class_name);
        }
        ZVAL_STRINGL(&function_name, integration.fname.ptr, integration.fname.len, 0);
        ZVAL_STRINGL(&loader, integration.loader.ptr, integration.loader.len, 0);

        ddtrace_defered_load_via_function(class_name, function_name, loader, 0);
#endif
    }
}

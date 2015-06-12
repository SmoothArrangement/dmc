/* global
 ajaxurl, templateInfo, DataProviderHelper, SessionTimeoutError, ErrorUtility, ServerPermissionError
*/

var DataProvider = {};
(function() {
    'use strict';

    function getCmsError(xhr) {
        var error = DataProviderHelper.validateRequest(xhr);
        if (!error) {
            var response = xhr.responseText;
            if (response === 'session_error' || response === '-1' || response === '0') {
                error = new SessionTimeoutError();
                error.loginUrl = templateInfo.login_page;
            } else if (typeof response === 'string') {
                var tag = '[permission_denied]';
                var parts = response.split(tag);
                if (parts.length >= 3 && parts[parts.length - 1] === '')
                    error = new ServerPermissionError(parts[parts.length - 2]);
            }
        }
        return error;
    }

    function ajaxFailHandler(url, xhr, status, callback) {
        var response = getCmsError(xhr);
        if (response) {
            callback(response);
        } else {
            var error = ErrorUtility.createRequestError(url, xhr, status, 'Request fail');
            callback(error);
        }
    }

    DataProvider.doExport = function doExport(exportData, callback) {
        var request = {
            'save': {
                'post': {
                    data: JSON.stringify(exportData),
                    action: "theme_template_export",
                    uid: templateInfo.user,
                    _ajax_nonce: templateInfo.nonce
                },
                'url': ajaxurl
            },
            'clear': {
                'post': {
                    action: "theme_template_clear",
                    uid: templateInfo.user,
                    _ajax_nonce: templateInfo.nonce
                },
                'url': ajaxurl
            },
            'errorHandler': getCmsError,
            'encode': true,
            'blob': true
        };
        DataProviderHelper.chunkedRequest(request, function(error, logs) {
            if (!error) {
                DataProvider.reloadTemplatesInfo(
                    function(error){
                        callback(error, logs);
                    }
                );
            } else {
                callback(error);
            }
        });
    };

    DataProvider.getTheme = function getTheme(themeName, callback) {
        if (!callback || typeof callback !== 'function') {
            throw DataProviderHelper.getResultError('Invalid callback');
        }

        themeName = (themeName || '' !== themeName) ? themeName : templateInfo.base_template_name;

        window.jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'text',
            data: ({
                action: 'theme_get_zip',
                themeName: themeName,
                uid: templateInfo.user,
                _ajax_nonce: templateInfo.nonce
            })
        }).done(function getThemeSuccess(data, status, xhr) {
            var error = getCmsError(xhr);
            if (error) {
                callback(error);
            } else {
                callback(null, data);
            }
        }).fail(function getThemeFail(xhr, status) {
            ajaxFailHandler(ajaxurl, xhr, status, callback);
        });
    };

    DataProvider.updatePreviewTheme = function updatePreviewTheme(callback) {
        if (!callback || typeof callback !== 'function') {
            throw DataProviderHelper.getResultError('Invalid callback');
        }

        window.jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: ({
                action: 'theme_update_preview',
                uid: templateInfo.user,
                _ajax_nonce: templateInfo.nonce
            })
        }).done(function updatePreviewSuccess(response, status, xhr) {
            var error = getCmsError(xhr);
            if (error) {
                callback(error);
            } else if (response.result === 'done' ) {
				DataProvider.reloadTemplatesInfo(function() {
					callback(null, {logs: response.log});
				});
            } else {
                var invalidResponseError = ErrorUtility.createRequestError(ajaxurl, xhr, status, 'updatePreview() server error: ' + response);
                callback(invalidResponseError);
            }
        }).fail(function updatePreviewFail(xhr, status) {
            ajaxFailHandler(ajaxurl, xhr, status, callback);
        });
    };




    DataProvider.load = function () {
        return templateInfo.projectData;
    };

    DataProvider.save = function save(saveData, callback) {
        var request = {
            'save': {
                'post': {
                    data: JSON.stringify(saveData),
                    action: "theme_save_project",
                    uid: templateInfo.user,
                    _ajax_nonce: templateInfo.nonce
                },
                'url': ajaxurl
            },
            'clear': {
                'post': {
                    action: "theme_template_clear",
                    uid: templateInfo.user,
                    _ajax_nonce: templateInfo.nonce
                },
                'url': ajaxurl
            },
            'errorHandler': getCmsError,
            'encode': true,
            'blob': true
        };
        DataProviderHelper.chunkedRequest(request, callback);
    };

    DataProvider.rename = function rename(themeName, callback) {
        if (!callback || typeof callback !== 'function') {
            throw DataProviderHelper.getResultError('Invalid callback');
        }

        window.jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: ({
                action: 'theme_rename',
                themeName: themeName,
                uid: templateInfo.user,
                _ajax_nonce: templateInfo.nonce
            })
        }).done(function renameSuccess(data, status, xhr) {
            var error = getCmsError(xhr);
            if (error) {
                callback(error);
            } else if('1' === data) {
                callback(null, window.location.href);
            } else {
                var invalidResponseError = ErrorUtility.createRequestError(ajaxurl, xhr, status, 'rename() server error: ' + data);
                callback(invalidResponseError);
            }
        }).fail(function renameFail(xhr, status) {
            ajaxFailHandler(ajaxurl, xhr, status, callback);
        });
    };

    DataProvider.canRename = function canRename(themeName, callback) {
        if (!callback || typeof callback !== 'function') {
            throw DataProviderHelper.getResultError('Invalid callback');
        }

        window.jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: ({
                action: 'theme_can_rename',
                themeName: themeName,
                uid: templateInfo.user,
                _ajax_nonce: templateInfo.nonce
            })
        }).done(function canRenameSuccess(data, status, xhr) {
            var error = getCmsError(xhr);
            if (error) {
                callback(error);
            } else if('true' === data) {
                callback(null, true);
            } else if('false' === data) {
                callback(null, false);
            } else {
                var invalidResponseError = ErrorUtility.createRequestError(ajaxurl, xhr, status, 'canRename() server error: ' + data);
                callback(invalidResponseError);
            }
        }).fail(function canRenameFail(xhr, status) {
            ajaxFailHandler(ajaxurl, xhr, status, callback);
        });
    };

    DataProvider.reloadTemplatesInfo = function reloadTemplatesInfo(callback) {
        if (!callback || typeof callback !== 'function') {
            throw DataProviderHelper.getResultError('Invalid callback');
        }
        window.jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: ({
                action: 'theme_reload_info',
                full_urls: true,
                uid: templateInfo.user,
                _ajax_nonce: templateInfo.nonce
            })
        }).done(function reloadInfoSuccess(data, status, xhr) {
            var error = getCmsError(xhr);
            if (!error) {
                try {
                    var info = JSON.parse(data);
                    $.each(info, function(key, value) {
                        templateInfo[key] = value;
                    });
                } catch(e) {
                    error = new Error(e);
                    error.args = { parseArgument: data };
                }
            }
            callback(error);
        }).fail(function reloadInfoFail(xhr, status) {
            ajaxFailHandler(ajaxurl, xhr, status, callback);
        });
    };

    DataProvider.getMaxRequestSize = function getMaxRequestSize() {
        return templateInfo.maxRequestSize;
    };

    DataProvider.backToAdmin = function backToAdmin() {
        // выкидывает на базовую страницу backend-а
        window.top.location = templateInfo.admin_url;
    };

    DataProvider.getAllCssJsSources = function getAllCssJsSources() {
        return templateInfo.cssJsSources;
    };

    DataProvider.getMd5Hashes = function getMd5Hashes() {
        return templateInfo.md5Hashes;
    };

    DataProvider.getInfo = function getInfo() {
        return {
            cmsName: 'WordPress',
            cmsVersion: templateInfo.cms_version,
            adminPage: templateInfo.admin_url,
            startPage: templateInfo.templates.home,
            templates: $.extend({}, templateInfo.templates),
            usedTemplateFiles: $.extend({}, templateInfo.used_template_files),
            canDuplicateTemplatesConstructors: $.extend({}, templateInfo.template_types),
            thumbnails: [{name: "screenshot.png", width: 600, height: 450}],
            isThemeActive: true,
            themeName: templateInfo.base_template_name,
            uploadImage: ajaxurl + '?action=theme_upload_image&uid=' + templateInfo.user + '&_ajax_nonce=' + templateInfo.nonce
        };
    };

    DataProvider.getFiles = function getFiles(mask, filter, callback) {
        if (!callback || typeof callback !== 'function') {
            throw DataProviderHelper.getResultError('Invalid callback');
        }
        if (mask === '*.css') {
            mask = '/bootstrap.css;/style.css';
        }
        window.jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: ({
                action: 'theme_get_files',
                mask: mask,
                filter:filter,
                uid: templateInfo.user,
                _ajax_nonce: templateInfo.nonce
            })
        }).done(function getFilesSuccess(data, status, xhr) {
            var error = getCmsError(xhr);
            if (error) {
                callback(error);
            } else if('string' === typeof data) {
                var files;
                try {
                    files = JSON.parse(data);
                } catch(e) {
                    error = new Error(e);
                    error.args = { parseArgument: data };
                    callback(error);
                    return;
                }
                callback(null, files);
            } else {
                var invalidResponseError = ErrorUtility.createRequestError(ajaxurl, xhr, status, 'getFiles() server error: ' + data);
                callback(invalidResponseError);
            }
        }).fail(function getFilesFail(xhr, status) {
            ajaxFailHandler(ajaxurl, xhr, status, callback);
        });
    };

    DataProvider.setFiles = function setFiles(files, callback) {
        if (!callback || typeof callback !== 'function') {
            throw DataProviderHelper.getResultError('Invalid callback');
        }

        var request = {
            'save': {
                'post': {
                    action: 'theme_set_files',
                    data: JSON.stringify(files),
                    uid: templateInfo.user,
                    _ajax_nonce: templateInfo.nonce
                },
                'url': ajaxurl
            },
            'clear': {
                'post': {
                    action: "theme_template_clear",
                    uid: templateInfo.user,
                    _ajax_nonce: templateInfo.nonce
                },
                'url': ajaxurl
            },
            'errorHandler': getCmsError,
            'encode': true,
            'blob': true
        };
        DataProviderHelper.chunkedRequest(request, callback);
    };
})();
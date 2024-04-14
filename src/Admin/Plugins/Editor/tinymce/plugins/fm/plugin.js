"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
var tinymce_1 = require("../../tinymce");
tinymce_1.default.PluginManager.add('fm', function (editor, url) {
    editor.ui.registry.addButton('test', {
        onAction: function (api) {
            console.log('hi there!');
        },
        text: 'Test!'
    });
    return {
        getMetadata: function () {
            return {
                name: 'FileManager',
                url: 'https://github.com/growtask/simflex-file-manager',
            };
        }
    };
});

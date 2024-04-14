import tinymce, {} from '../../tinymce';

tinymce.PluginManager.add('fm', (editor, url) => {
    editor.ui.registry.addButton('test', {
        onAction: (api) => {
            console.log('hi there!');
        },
        text: 'Test!'
    });

    return {
        getMetadata: () => {
            return {
                name: 'FileManager',
                url: 'https://github.com/growtask/simflex-file-manager',
            };
        }
    };
});

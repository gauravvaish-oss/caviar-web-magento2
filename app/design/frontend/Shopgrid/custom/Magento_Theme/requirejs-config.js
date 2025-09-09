var config = {
    paths: {
        'bootstrap': 'js/bootstrap',
        'glightbox': 'js/glightbox.min',
        'tinyslider': 'js/tiny-slider'
    },
    shim: {
        'bootstrap': {
            deps: ['jquery']
        },
        'glightbox': {
            exports: 'GLightbox'
        },
        'tinyslider': {
            exports: 'tns'
        }
    },
};

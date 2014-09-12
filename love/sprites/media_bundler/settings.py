MEDIA_BUNDLES = (
    {"type": "javascript",
     "name": "myapp_scripts",
     "path": MEDIA_ROOT + "/scripts/",
     "url": MEDIA_URL + "/scripts/",
     "minify": True,  # If you want to minify your source.
     "files": (
         "foo.js",
         "bar.js",
         "baz.js",
     )},
    {"type": "css",
     "name": "myapp_styles",
     "path": MEDIA_ROOT + "/styles/",
     "url": MEDIA_URL + "/styles/",
     "minify": True,  # If you want to minify your source.
     "files": (
         "foo.css",
         "bar.css",
         "baz.css",
         "myapp-sprites.css"  # Include this generated CSS file.
     )},
    {"type": "png-sprite",
     "name": "myapp_sprites",
     "path": MEDIA_ROOT + "/images/",
     "url": MEDIA_URL + "/images/",
     # Where the generated CSS rules go.
     "css_file": MEDIA_ROOT + "/styles/myapp-sprites.css",
     "files": (
         "foo.png",
         "bar.png",
         "baz.png",
     )},
)


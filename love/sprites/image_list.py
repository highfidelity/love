import os

# just add the images which are needed for the sprite to the following list.
# the resulting sprite is saved in images/lm-sprites.png
# the css file is saved in css/lm-sprites.css

files = (
 "chart.png",
 "customLogo.png",
 "checked.gif",
 "arrow-down.png",
 "arrow-up.png",
 "arrow1.png",
 "background_gradient4.jpg",
)




# define the path to the images directory
BASE_DIR = os.path.dirname(os.path.abspath(__file__)) + '/..'
# url to refer the images to, can be absolute or relative
CSS_URL = ''

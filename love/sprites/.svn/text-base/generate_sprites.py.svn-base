#!/usr/bin/python
from media_bundler import bundler
import os
import image_list


def key(bundle):
  return -int(isinstance(bundle, bundler.PngSpriteBundle))

def main():
  print "Don't forget to add new images to the file list in image_list.py\n\n"

  print "You can find the generated sprite in ../images/lm_sprites.png"
  print "the css file is located in ../css/lm_sprites.css\n"
  bundles = sorted(bundler.get_bundles().itervalues(), key=key)
  bundles[0].make_bundle(None)
  print "sprite generation successful"
  
if __name__ == "__main__":
  main()

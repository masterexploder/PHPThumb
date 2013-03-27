# PHP Thumb

*Notice!!*
This project will soon be at version 2.0! Their will be major API changes and backward-compatability breaks. At this time we are no longer accepting pull requests for the 1.0 branch. [Go to the 2.0 Branch](https://github.com/masterexploder/PHPThumb/tree/2.0).

Also note - the 2.0 branch is not yet stable. Feel free to test, but I would recommend against using it in production, as the API may still change.


PHP Thumb is a light-weight image manipulation library 
aimed at thumbnail generation. It features the ability to 
resize by width, height, and percentage, create custom crops, 
or square crops from the center, and rotate the image. You can 
also easily add custom functionality to the library through plugins. 
It also features the ability to perform multiple manipulations per 
instance (also known as chaining), without the need to save and 
re-initialize the class with every manipulation.

More information and documentation is available at the project's 
homepage: [http://phpthumb.gxdlabs.com](http://phpthumb.gxdlabs.com)

## Documentation / Help

I've tried to thoroughly document things as best I can, but here's a list of places to 
find documentation / help:

- [Documentation](https://github.com/masterexploder/PHPThumb/wiki) - Your best friend, the library docs
- [Forums](http://phpthumb.gxdlabs.com/forums) - Got questions, comments, or feedback? This is the place to visit
- [Developer Docs](http://phpthumb.gxdlabs.com/apidocs) - Auto-generated docs for developers… these cover the code itself

## Developing

While I know it's somewhat discriminatory in the PHP world since many of
you are still on windows (thus making working with ruby a bit more difficult), 
I've created a rakefile that takes care of common tasks (such as generating docs, 
building release tarballs, etc.)  This probably isn't relevant to anything you 
would be doing unless you want to fork this project and release your version of it.

On that note, if you'd like to contribute to the project, you can either fork it and
submit back to me, or you can ask to be a contributor.  Reach out to me if you're 
interested, I'm happy to chat :)

## License

PHP Thumb is released under MIT license.

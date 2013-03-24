BlueShoes JavaScript Tree Component
===================================

NOTE: 

 - If you use this as standalone package then you may want to 
   change the javascript include path (which currently is 
   /_bsJavascript/components/tree/) to something else. see 
   the examples.
   in addition, if you don't use the "/_bsJavascript" solution, 
   you will have to change the image path of your Bs_Tree instance.
   example:
   tree = new Bs_Tree();
   tree.imageDir = 'img/win98/'; //or whatever you please
   
   we use apache's "alias" to create a virtual root. this can be 
   done in microsoft iis aswell. in apache it looks something like:
   Alias /_bsJavascript   /usr/local/lib/php/blueshoes-4.4/javascript


 - The Bs_TreeEditor.html is experimental and not up to date.

 - The "checkbox" class is only needed if you use the checkbox feature 
   of the tree class. see the examples.

 - For questions etc please go to: 
   http://developer.blueshoes.org/forum/

 - the website for the tree is here: 
   http://www.blueshoes.org/en/javascript/tree/

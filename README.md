[![Build Status](https://travis-ci.org/urbanetter/Muentschi.png?branch=master)](https://travis-ci.org/urbanetter/Muentschi)

What
====

Muentschi offers a conextual approach for displaying your view. Instead of just defining
a bunch of markup tags, it allows you to treat the objects on your view semantically and define how they look.

As examples, Muentschi allows you to write

    $table->select('column:empty')->insteadOf('content')->add('text', 'This is an empty column!');
    $table->select('row:even')->add('tr', array('class' => 'even_row'));

instead of nested if statements in your view template.

A more complete example to get you started:

    // a table
    $table = new Muentschi('table');
    $table->add('htmlTag', 'table'); // surrounding html tag <table>
    $table->add('contexts', 'row');  // a table consists of rows
    $table->select('row')->add('htmlTag', 'tr'); // <tr> surround rows
    $table->select('row')->add('contexts', 'column'); // rows consist of columns
    $table->select('column')->add('htmlTag', 'td'); // columns are surrounded by <td>
    $table->select('column')->add('content'); // finally the content is displayed
    
    // add class 'alt' to every second <tr> tag in the table
    $table->select('row:even')->add('htmlTag', array('tag' => 'tr', 'class' => 'alt'));
    
    // replace the table with a div and a message if the table is empty
    $table->select('table:empty')->replace()->add('htmlTag', 'div')->add('text', 'No rows to display!');
    
    // setting the content and render the context
    $content = array(1 => array('foo', 'bar'), 2 => array('baz', 'bat'));
    $table->setContent($content);
    echo $table->render();
    $this->view->table = $table; // In a controller in Zend Framework

Why
===
Everything is an object today. MVC learned us to treat data as models. Often a model is mapped to one or more database tables, there are Active Record implementations which allow to treat a record of a table as object.
The only place we do not treat our data as objects is in the output, the view. We still write lots of HTML tags, often folded into different if statements
because we have to for example display the sorted column in a table differently.

Muentschi allows you to treat your data as it is. In its context. Instead of checking for iteration number you can specify the HTML class of the first row. And all this as extension to your chosen template engine
(or PHP or whatever you use for your view). Muentschi integrates well with any template engines. There are integrations for Zend Framework
and Smarty.

Selectors
=========
The following selectors are implemented:

* name
* name#id
* name.tag
* name[foo]
* name:odd, name:even, name:first, name:last, name:empty
 
name
----
Its possible to select contexts by the name of the context itself. To add something to all rows use select('row')

name#id
-------
Every instance of a context has its own id. Normally, the id of the context is given by the key of the array. To select the column which displays the first name use select('column#firstname')

name.tag
--------
Tags can be used to mark some context. Think of it like a CSS class. To select all columns which have the tag "sorted", use select('column.sorted').
The context needs to know which contexts have which tags, for setting a tag use addTag('sorted'). Tags get inherited by the sub contexts. If you
set a tag to a row context, the column context of this row also have the tag. Check this by using hasTag().

name[foo]
---------
Select contexts which have a certain content. To select all columns with the content foo use select('column[foo]'), to select the row with the id 15, use select('row[id=15]')

name:odd, name:even, name:first, name:last, name:empty
------------------------------------------------------
The computed tags allow to select certain contexts: Use

* row:odd to select all odd rows
* row:even to select all even rows
* row:first for the first row
* row:last for the last row
* table:empty for contexts with empty content
 
Decorators
==========
Decorators are used to finally render the context. Decorators append, prepend or replace the output with something and are rendered from inside out. Use the function add() to add a decorator for a context.

    $context = new Muentschi('simple');
    $context->add('htmlTag', 'h1');
    $context->add('text', 'Hello world!');
    print ($context->render());
    
When rendering this with $context->render(), first the text 'Hello world!' is rendered and given to the HtmlTag decorator. This wraps the received text by a h1 tag. The result is &lt;h1&gt;Hello world!&lt;/h1&gt;.

The second parameter of the function add() could also be an array of options. If a string is given, this is set as main option of the decorator.
The placement option is available for all decorators and allows to specify where to put the decorators output. Use the value 'prepend' to prepend the output,
'append' to eppend it. Writing $context->add('htmlTag', array('tag' => 'h1', 'placement' => 'append'); would result in &lt;h1&gt;&lt;/h1&gt;Hello world!

The second general available option is 'separator'. It allows to specify a spearator which  is inserted between the decorator output and the given output.
$context->add('htmlTag', array('tag' => 'h1', 'separator' => ' == '); would result in &lt;h1&gt; == Hello world! == &lt;/h1&gt;.

Every option in the decorators allows to access the content of the context by placeholders. The placeholders are sourrounded by curly brackets.

    $context = new Muentschi('text);
    $context->add('text', 'Hello {text}!');
    $context->setContent('world');
    print ($context->render());

This would result in "Hello world!". It is also possible to access content of parent contexts. Use the syntax "contextName.content" for this. In a table you can use {row.id} to access the id of the row.

The following decorators are provided:

* Text To output general text
* Content To output content
* HtmlTag To render a HTML tag
* Context To render another context
* Contexts To render multiple other contexts

Content
=======
When using sub contexts, the content is expected to be an array, or an object which implements ArrayAccess. When creating sub contexts, a context for every
entry in the array is created. This allows to build tables quite easily. With the option "ids" it's possible to specify which keys should be taken for
context generation.

    $context = Muentschi('list');
    $context->add('htmlTag', 'ul');
    $context->add('contexts', 'li');
    $context->select('li')->add('htmlTag', 'li');
    $context->select('li')->add('content');
    
    $content = array('key1' => 'item 1', 'key2' => 'item 2', 'key3' => 'item 3');
    $context->setContent($content);
    print ($context->render());    

When rendering the contexts decorator, a new sub context with the name "li" is created for every item in the array. The id of the context is the key of the item in the array-
This means that eg. the first "li" context has the id "key1" and is selectable by select('li#key1').

Merging selectors
=================
Chances are that not only one selector matches for a context. For the first row for example, the 'row' selector applies, but the selector 'row:first' as well.
Muentschi sorts the selectors before applying them. The less specific the selector is, the bigger the chances that it gets applied first.

By default, the decorators get merged, meaning the options of the decorators of the more specific selector get merged with the ones from the less specific ones.
In this case, "merging" means "overriding". An exception is the "class" option of the HtmlTag decorator: the options get appended. See the function merge() in the specific decorator class.

    $context->select('row')->add('htmlTag', array('tag' => 'h1', class => 'myRowClass');
    $context->select('row:first')->add('htmlTag', array('tag' => 'h2', class => 'myFirstRow');

This code will result in the options tag="h1" and class="myRowClass" for every row except the first one which will be tag="h2" and class="myRowClass myFirstRow"

The merging behaviour is just the default behaviour. On every selector it is possible to set how they get merged to another decorator. The possibilities are:

* merge: The default
* replace: All decorators get replaced by the decorators of this selector
* before: The decorators of this selector get inserted before a specific decorator
* after: The decorators of this selector get inserted after a specific decorator
* insteadOf: The decorators get inserted instead of another decorator
* remove: A specifc decorator gets removed.

To specify a specific behaviour, call the according function on the selector:
    $context->select('column:empty')->insteadOf('content')->add('text', 'This is an empty column!');

For before, after, insteadOf and remove a name of a decorator is required. The name of the decorator is for HtmlTags the tag, for contexts the context name and for content 'content'. See the function getName() in the decorator class.

# Share

A helper module for pdf-print, print and social share functionality based on bootstrap to custom modules.


## Features

- news & calendar support
- add syndication to modules and articles
- syndication options:
    - print pdf
    - print by a custom module template or call the default browser print fun
    - ical-event
    - mailto
    - feedback
    - facebook share
    - twitter share
    - googleplus share


## Usage

### Setup for modules

##### Add Share to your module palette

Add `{share_legend},addShare;` to your model default palette.

Example: 
```
$dca = &$GLOBALS['TL_DCA']['tl_module'];
$dca['palettes']['newsreader'] = str_replace(
    '{image_legend',
    '{share_legend},addShare;{image_legend',
    $dca['palettes']['newsreader']
);
```


##### Generate share output

To show the share buttons in your module, you need to add the return of `Settings->generate()` to your template.

Example:

```php
// Module class: 

public function compile()
{
    ...
    $this->generateShare();
    ...
}

protected function generateShare()
{
    if ($this->addShare)
    {
        $objShare = new \HeimrichHannot\Share\Share($this->objModel, [Entity to print]);

        $this->template->share = $objShare->generate();
    }
    return null;
}

// Template file:

<?= $this->share ?>
```

#### Add Share urls to your template

```php
// Add this to the generateShare method from above, after calling generate():
if ($this->module->share_addTemplateLinks)
{
    $this->template->shareUrls = $objShare->generateShareUrls();
}
```

This will add an array to the template with following key containing just the urls:
* mailto
* facebook
* twitter
* linkedin
* whatsapp


### Setup for articles
Since version 1.5 you can also print complete articles.

1. Setup a new model, which has share enabled (=has addShare added to the module palette, the module type doesn't matter). This module will hold the settings for the article.

2. Check "Add Syndication" on the article settings page and choose the module setup before.

3. Echo `$this->share` in your article template.


### Custom name for your pdf files

Your module has to implement `ModulePdfReaderInterface`. The return-value of  `getFileName()` is used as pdf file name. Don't add .pdf, it will be added by the module itself.

1
### Print page

To address your custom module print layout, the url must contain the `print` parameter with the module id as value. (Example: `?print=57`). 
The default print link will do that for you and will create a new tab/window and close it immediately after the window was printed by the user.
To debug the print layout, add the `pDebug=1` parameter to your print url (Example: `?print=57&pDebug=1`).

For regions within your templates that should not be printable, add `<!-- print::stop -->` before that region and `<!-- print::continue -->` afterwards.

```
<!-- print::stop -->
DO NOT PRINT THIS!
<!-- print::continue -->
```

It is also possible to simple call the browser print windows to print the complete page by checking 'printWithoutTemplate' withing the syndication selection in module settings.
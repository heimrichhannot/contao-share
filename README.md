# Share

A helper module for pdf-print, print and social share functionality based on bootstrap to custom modules.


## Features

- news & calendar support
- pdf-print-button
- print-button
- ical-event-button
- mailto-button
- feedback-button
- dropdown menu with facebook/twitter/googleplus share

## Usage

### Add Share to your module palette

Add the following Syntax to your module palette:
```
$dc['palettes']['your-module'] = ...{share_legend},addShare;...
```

### Generate share output example
Use the return value in your module to render share links, if choosen in the module settings.

```
protected function generateShare()
    {
        if ($this->addShare)
        {
            $objShare = new \HeimrichHannot\Share\Share($this->objModel, [Entity to print]);

            return $objShare->generate();
        }
        return null;
    }
```

### Custom name for your pdf files

Your module has to implement `ModulePdfReaderInterface`. The return-value of  `getFileName()` is used as pdf file name. Don't add .pdf, it will be added by the module itself.


### Template Syntax

Add the following Syntax to your templates, to provide share links.

```
<?= $this->share; ?>
```

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
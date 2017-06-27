# Share

A helper module for pdf-print, print and social share functionality based on bootstrap to custom modules.


## Features

- news & calendar support
- pdf-print-button
- print-button
- ical-event-button
- dropdown menu with facebook/twitter/googleplus share

## Usage

### Add Share to your module palette

Add the following Syntax to your module palette:
```
$dc['palettes']['your-module'] = ...{share_legend},addShare;...
```

### Generate share output example

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


### Template Syntax

Add the following Syntax to your templates, to provide share links.

```
<?= $this->share; ?>
```
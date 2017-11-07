# Color Chooser Field

Maintainer: Deux Huit Huit (<https://deuxhuithuit.com>)    
Original Author: Josh Nichols (mrblank@gmail.com)    


#### Installation

1. Upload the folder in this archive to your Symphony 'extensions' folder and rename it to `color_chooser_field`.

2. Enable it by selecting the "Field: Color Chooser", choose Enable from the with-selected menu, then click Apply.

3. You can now add a "Color Chooser" field to your sections.

4. On the entry edit screen, it uses the Farbtastic Color Picker [http://acko.net/dev/farbtastic](http://acko.net/dev/farbtastic) to visually select a color and place its hex value into the field.

See a screencast of the field in action: [http://www.vimeo.com/6062027](http://www.vimeo.com/6062027)

#### Usage

The field returns

```xml
<field-name r="decimalRedValue" g="decimalGreenValue" b="decimalBlueValue" c="printCyanValue" m="printMagentaValue" y="printYellowValue" k="printBlackValue" brightness="0to100brightnessValue" has-color="yes|no">#HexValue</field-name>
```

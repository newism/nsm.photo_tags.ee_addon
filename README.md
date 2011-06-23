# NSM Photo Tags

NSM Photo Tags is an image tagging system for ExpressionEngine 2 designed to be used for creating interactive galleries. 

NSM Photo Tags compliments Pixel & Tonic's Matrix field type to let website administrators add selection regions to an image by adding Matrix rows and resizing the selection box. The position and dimensions for these selections are accessible in the custom field's tag data pair for website designers to output the selection regions using `nsm_photo_tags_top`, `nsm_photo_tags_left`, `nsm_photo_tags_width` and `nsm_photo_tags_height` tags (prefixed with `nsm_photo_tags_` to minimise conflicts with other modules).

* Multiple position field types can be used in a single Matrix row to create different possibilities (eg: store a hit-box in one column and a css-based glow effect's position in another column).
* Co-ordinates are stored in the database as grid-position co-ordinates (we may consider providing a means to access this raw data for HTML5 Canvas applications).
* Simple to use.
* Infinite possibilities by mixing and matching different field-types in your Matrix custom field (eg: a Matrix row could store a title, caption, Playa link to another entry, hit-box area and then an overlay position for a neat image-swap effect).

## Images

* [Report index](http://cl.ly/093h1O35093p2n201l0q)

## Developers

To use this custom field-type you will need a Matrix custom field and an Expression Engine File field type that accepts images only. The Matrix field will contain the NSM Photo Tags in one of it's columns and the File field will be used as the 'canvas' for the Photo Tags.

### Adding the Custom Field

* It is best to add the image File field to the channel custom fields first to save time (eg: our image field is named `nsm_photo_tags_test_image`).
* Add the Matrix field that will be used for your Photo Tags (eg: name this Matrix field `nsm_photo_tags_test_matrix`).
* Set one of the Matrix column field-types to NSM Photo Tags and name the column as appropriate (eg: our NSM Photo Tags is named `nsm_photo_tags_test_overlay_pos`).
* In the field settings for this NSM Photo Tags choose the image that will be used as the 'canvas' using the select input (eg: choose the option for our `nsm_photo_tags_test_image` field).
* Save the new custom field.

### Tag Parameters

#### prefix

By default the single tag variables used inside a NSM Photo Tags tag pair will be prefixed with `nsm_photo_tags_` to minimise the chance that conflicts could arise where multiple modules or plugins may use tag names such as `width`, `height`, `top` or `left`. You can specify a tag pair's tag variable prefix to use by setting a new value in the `prefix`. If you set a blank value in the `prefix` parameter this will remove the prefix for the tag pair.

### Single Tag Variables

#### _prefix_+width

Returns the width of the selection box. This is calculated by subtracting the X position of the top-left set of co-ordinates from the X position top-right set of co-ordinates.

#### _prefix_+height

Returns the height of the selection box. This is calculated by subtracting the Y position of the top-left set of co-ordinates from the Y bottom-left set of co-ordinates.

#### _prefix_+top

Returns the vertical offset of the selection box from the top of the canvas. This is calculated by returning the Y position of the top-left set of co-ordinates.

#### _prefix_+left

Returns the horizontal offset of the selection box from the left side of the canvas. This is calculated by returning the X position of the top-left set of co-ordinates.

### Usage

Now that your custom field is set up you will need to add some tag data to your Expression Engine templates that will display the NSM Photo Tags. NSM Photo Tags wraps its tag variables in a tag pair in a similar fashion to Matrix. The positioning data is stored as single tag variables.

* Add the canvas for our Photo Tags using either an image or a CSS background image (eg: our image source will be `{nsm_photo_tags_test_image}`).
* Add the Matrix field tag pair where needed (eg: `{nsm_photo_tags_test_matrix}`).
* One way of visualising your Photo Tags might be by using 'div' elements for each Matrix row (and then another `div` for each Matrix column that uses NSM Photo Tags as the field-type)
* Inside this Matrix tag pair add the NSM Photo Tags tag pair where it will be needed (eg: we wrap the contents of `div` element's `style` attribute with `{nsm_photo_tags_test_overlay_pos}...{/nsm_photo_tags_test_overlay_pos}`).
* Use the `nsm_photo_tags_top`, `nsm_photo_tags_left`, `nsm_photo_tags_width` and `nsm_photo_tags_height` tags as needed inside the containing tag pair (we have used these tags with their corresponding CSS style key, eg: `top:{nsm_photo_tags_top}px; left:{nsm_photo_tags_left}px`).

## Website Administrators

Adding the selection areas for your NSM Photo Tags is simple to do (and will be made easier as we release updated versions).

* Create a new Channel Entry in the channel used to store your galleries.
* Choose or upload an image to use in the File field that you chose to target during the set up process (eg: we use `nsm_photo_tags_test_image`).
* Save the entry and follow the link to Edit the entry again.
* You will notice that above the Matrix custom field we added for our NSM Photo Tags there is now a large version of the image you chose earlier.
* To add a new selection area to the canvas add a new Matrix row.
* A new box has been added to the canvas and can be dragged and resized to cover the desired area of the image.
* Repeat for each selection overlay that you want to add to the image.

If you want to highlight a selection box on the canvas you can:

* Click on a selection box.
* Click the Select button inside the Matrix cell that you are working in and the corresponding selection box will be highlighted on the canvas.

The Reset button inside of a Matrix cell will return the corresponding selection box to its original default values. This is a handy function if you choose a canvas image that is smaller than the old image dimensions and your selection boxes seem to have 'disappeared'.

![Report Index](http://cl.ly/093h1O35093p2n201l0q/Screen_shot_2011-03-07_at_7.44.43_PM.png)    
Report index

![Report Details](http://cl.ly/191h110z1Z08401h1d02/Screen_shot_2011-03-07_at_7.58.39_PM.png)    
Report details w/ browser output

![Report Details](http://cl.ly/2m2h0z240i3f1x0b3x08/Screen_shot_2011-03-07_at_7.57.57_PM.png)    
Report details w/ save as preset inputs

![Saved Reports](http://cl.ly/3B3f0T1f06421C1p2t0P/Screen_shot_2011-03-07_at_7.59.56_PM.png)    
Report details w/ save as preset inputs







www.sxc.hu Image ID: 812716
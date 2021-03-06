(function($) {

	// plugin definition
	$.fn.NSM_PhotoTags = function(options) {
		var opts = $.extend({}, $.fn.NSM_PhotoTags.defaults, options);
		return this.each(function() {
			var $self = $(this);
			var widget = {
				opts: opts,
				strokes: [],
				$canvas: false
			}

			$self.data("nsm_photo_tags_widget", widget);

			// build the canvas
			$.NSM_PhotoTags._initCanvas($self);

		});
		
	};

	$.NSM_PhotoTags = {

		_initCanvas: function(widget_el) {
			var widget = $(widget_el).data('nsm_photo_tags_widget');
			var $canvas = widget.$canvas;
			if(!$canvas) {
				$image = $("<img />")
							.addClass('nsm_photo_tags_image');
				$canvas = $("<div />")
							.addClass('nsm_photo_tags_canvas')
							.data("nsm_photo_tags_img", $image)
							.prepend($image)
							.prependTo(widget_el)
							.bind("click", function(event){
								$.NSM_PhotoTags._selectCanvasStroke(event);
							});

				widget.$canvas = $canvas;
			}
			var imageUrl = $.NSM_PhotoTags._getCanvasImageUrl(widget.opts.src_image_field_id);
			$.NSM_PhotoTags._updateCanvasImage(widget_el, imageUrl);
			
		},

		_getCanvasImageUrl: function(src_image_field_id) {
			console.log("input[name='field_id_"+(src_image_field_id)+"_hidden_dir']");
			var upload_dir_id = $("input[name='field_id_"+(src_image_field_id)+"_hidden_dir']").val();
			var upload_dir = EE.upload_directories[upload_dir_id]['url'];
			var image_name = $("input[name='field_id_"+(src_image_field_id)+"_hidden']").val();
			return upload_dir + image_name;
		},

		_updateCanvasImage: function(widget_el, imageUrl) {
			var widget = $(widget_el).data('nsm_photo_tags_widget');
			var $canvas = widget.$canvas;
			var $image = $canvas.data('nsm_photo_tags_img');
			
			$image.attr('src', imageUrl);
			if($image.width() > 0){
				$canvas.width( $image.width() );
			}
			if($image.height() > 0){
				$canvas.height( $image.height() );
			}
		},
		
		_drawStroke: function(widget_el, coords, cell) {
			// create the element <div>
			// set the top left right bottom
			var $stroke = $.NSM_PhotoTags._drawStrokeElement(coords);
			$stroke.data('nsm_photo_tags_cell', cell);
			// insert that box into the canvas
			// setup cell event
			// store a data reference to the box on the cell
			// bind a click on the cell to highlight
			var widget = $(widget_el).data('nsm_photo_tags_widget');
			var $canvas = widget.$canvas;
			$canvas.append($stroke);

			// setup the box events
			// drag, resize
			// on drag resize
			// update the cell
			$stroke.draggable({
						containment:'parent',
						stop: function(event, ui) {
							$.NSM_PhotoTags._updatePos(event, ui);
						}
					})
					.resizable({
						containment:'parent',
						stop: function(event, ui) {
							$.NSM_PhotoTags._updatePos(event, ui);
						}
					});
			widget.strokes.push($stroke);
			return $stroke;
		},
		
		_drawStrokeElement: function(coords) {
			var dimensions = {
				"position":"absolute",
				"top":Number(coords[0][0]),
				"left":Number(coords[0][1]),
				"width":Number(coords[1][1])-Number(coords[0][1]),
				"height":Number(coords[3][0])-Number(coords[0][0])
			}
			var el = $('<div/>')
						.addClass('nsm_photo_tags_stroke')
						.css(dimensions);
			return el;
		},
		
		_removeStroke: function($cell) {
			// get the box referenced form the cell, destroy it
			var $stroke = $cell.data('nsm_photo_tags_stroke');
			$stroke.remove();
		},
		
		_selectCanvasStroke: function(event) {
			var $canvas = $(event.currentTarget);
			var $field = $canvas.parent();
			var $selected = $(event.target);
			if($selected.hasClass('nsm_photo_tags_stroke')){
				var widget = $field.data('nsm_photo_tags_widget');
				for(var i in widget.strokes){
					widget.strokes[ i ].removeClass('active');
				}
				$selected.addClass('active');
			}
		},
		
		_selectCellStroke: function(event) {
			var $cell = $(event.currentTarget);
			var $field = $cell.data('nsm_photo_tags_field');
			var $canvas = $field.find('div.nsm_photo_tags_canvas');
			var widget = $field.data('nsm_photo_tags_widget');
			var $selected = $cell.data('nsm_photo_tags_stroke');
			for(var i in widget.strokes){
				widget.strokes[ i ].removeClass('active');
			}
			if($selected){
				$selected.addClass('active');
			}
			
			var $target = $(event.target);
			var action = $target.attr('data-action');
			if(typeof action !== 'undefined'){
				$.NSM_PhotoTags[ action ]($cell);
			}
			
		},
		
		_updatePos: function(event, ui) {
			var $self = $(ui.helper);
			var $cell = $self.data('nsm_photo_tags_cell');
			var coords = $.NSM_PhotoTags._convertToVector.fromRectangle(ui.helper);
			$('textarea.nsm_photo_tags_dataval', $cell).val(coords);
			return false;
		},
		
		_resetPos: function($cell) {
			var $selected = $cell.data('nsm_photo_tags_stroke');
			$selected.css({"top":10, "left":10}).width(50).height(50).trigger('resize');
		},
		
		_convertToVector: {
			fromRectangle: function($el) {
				var pos = $el.position();
				var size = {
								"width": $el.width(),
								"height": $el.height()
							};
				var coords = '[';
				coords +=		'[' + Number(pos.top) + ',' + Number(pos.left) + '],';
				coords += 		'[' + Number(pos.top) + ',' + Number(pos.left + size.width) + '],';
				coords += 		'[' + Number(pos.top + size.height) + ',' + Number(pos.left + size.width) + '],';
				coords += 		'[' + Number(pos.top + size.height) + ',' + Number(pos.left) + ']';
				coords += 	']';
				return coords;
			}
		}
	};

	$.fn.NSM_PhotoTags.defaults = {
		
	};


	// Bind show cell for any instance of the fieldtype
	Matrix.bind('nsm_photo_tags', 'display', function(cell) {
		var data_str = cell.dom.$inputs.val();
		var $cell = $(cell.dom.$td);
		$cell.data('nsm_photo_tags_field', $('#'+cell.field.id) ).bind("click", function(event){
			$.NSM_PhotoTags._selectCellStroke(event);
		});
		if(!data_str) {
			return;
		}
		var coords = $.parseJSON(data_str);
		var $stroke = $.NSM_PhotoTags._drawStroke(cell.field.dom.$field, coords, $cell);
		$cell.data('nsm_photo_tags_stroke', $stroke);
	});

	// Bind remove cell for any instance of the fieldtype
	Matrix.bind('nsm_photo_tags', 'remove', function(cell) {
		$.NSM_PhotoTags._removeStroke(cell.dom.$td);
	});


})(jQuery);
(function($){
	
	var settings = {
		strokes:[],
		target_field: null,
		this_field: null,
		zone: null
	};
	
	var methods = {
		init: function(options){
			
			return this.each(function(){
				
				if(options){
					$.extend( settings, options );
				}
				settings.this_field = String(this.id).substring(9);
				
				var upload_dir_id = $("input[name='field_id_"+(settings.target_field)+"_hidden_dir']").val();
				var upload_dir = EE.upload_directories[upload_dir_id]['url'];
				var image_name = $("input[name='field_id_"+(settings.target_field)+"_hidden']").val();
				var image_url = upload_dir + image_name;
				// assemble image html and prepend to table
				var image_html = $('<img class="nsm_ig_canvas" src="'+image_url+'" alt="large image" id="field_id_'+settings.this_field+'_image"/>');
				// prepare the construction site
				settings.container_id = 'field_id_'+settings.this_field+'_ui_container';
				settings.zone = $('<div class="nsm_ig_container" id="'+settings.container_id+'"></div>').prependTo( $("#field_id_"+settings.this_field+'') );
				// append image and fix width to image size
				settings.zone.prepend(image_html);
				
				Matrix.bind('nsm_interactive_gallery', 'display', function(cell){
					var $d = cell.dom.$inputs;
					// add to canvas
					var stroke = {
						"this_id":cell.field.id +'_'+ cell.row.id +'_'+ cell.col.id,
						"field_id":cell.field.id,
						"row_id":cell.row.id,
						"col_id":cell.col.id,
						"dimensions":{
							"top":Number($d.eq(0).val()),
							"left":Number($d.eq(1).val()),
							"width":Number($d.eq(2).val()),
							"height":Number($d.eq(3).val()),
							"zIndex":50
						}
					};
					$("#"+stroke.field_id).NsmInteractiveGallery('add', stroke);
					// bind select button
					var select_btn = cell.dom.$td.find('button.nsm_ig_select');
					select_btn.bind('click', function(event){
						event.preventDefault();
						var id = cell.field.id+'_'+cell.row.id+'_'+cell.col.id;
						$("#"+options.field_id).NsmInteractiveGallery('select', id);
						return false;
					});
				});
				// on delete row
				Matrix.bind('nsm_interactive_gallery', 'remove', function(cell){
				  $("#"+cell.row.id).remove();
				});
				
			});
		},
		add: function(options){
			// default
			var dimensions = {
				"top":5,
				"left":5,
				"width":50,
				"height":50,
				"zIndex":50
			}
			// merged
			if(typeof(options.dimensions) !== 'undefined'){
				for(p in dimensions){
					if(typeof(options.dimensions) !== 'undefined' && options.dimensions[p] > 0){
						dimensions[p] = options.dimensions[p];
					}
				}
			}
			// create new selection box
			var $s = $('<div/>')
						.attr('id', options.field_id+'_'+options.row_id+'_'+options.col_id)
						.data('nsm_ig', {
							"canvas": settings.this_field,
							"field": options.field_id,
							"row": options.row_id,
							"col": options.col_id
						})
						.addClass('nsm_ig_stroke')
						.css(dimensions)
						.css('position', 'absolute')
						.bind('click', function(){
							var id = this.id; 
							$("#"+options.field_id).NsmInteractiveGallery('select', id);
							return false;
						})
						.draggable({
							stop: function(event, ui) {
								$("#"+options.field_id).NsmInteractiveGallery('updatePos', ui);
							}
						})
						.resizable({
							stop: function(event, ui) {
								$("#"+options.field_id).NsmInteractiveGallery('updatePos', ui);
							}
						})
						.appendTo(settings.zone);
			settings.strokes.push($s);
			return this;
		},
		updatePos:function(ui){
			var $t = $(ui.helper);
			var this_id = $t.attr('id');
			var ig_data = $t.data('nsm_ig');
	
			var input_place = ig_data.field+'\\['+ig_data.row+'\\]\\['+ig_data.col+'\\]';
	
			if(typeof(ui.position) !== 'undefined'){
				$("input[name='"+input_place+"\\[top\\]']").val(ui.position.top);
				$("input[name='"+input_place+"\\[left\\]']").val(ui.position.left);
			}
			if(typeof(ui.size) !== 'undefined'){
				$("input[name='"+input_place+"\\[width\\]']").val(ui.size.width);
				$("input[name='"+input_place+"\\[height\\]']").val(ui.size.height);
			}
			return this;
		},
		select: function(id){
			var $t = $('#'+id);
			var this_id = $t.attr('id');
			var ig_data = $t.data('nsm_ig');
			console.log(settings.strokes);
			var brushes = settings.zone.find('div.nsm_ig_stroke');
			brushes.removeClass('active');
			brushes.filter($t).addClass('active');
			return this;
		}
	};
	
	$.fn.NsmInteractiveGallery = function(method){
		
		// Method calling logic
		if(methods[method]){
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		}else if(typeof method === 'object' || ! method){
			return methods.init.apply(this, arguments);
		}else{
			$.error('Method '+ method+' does not exist on jQuery.NsmInteractiveGallery');
		}
		
	}
	
})(jQuery);
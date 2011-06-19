nsm_interactive_gallery = {
	prep:[],
	strokes:{},
	canvases:{},
	init: function(){
		for(i=0,m=nsm_interactive_gallery.prep.length; i<m; i+=1){
			nsm_interactive_gallery.createCanvas( nsm_interactive_gallery.prep[ i ] );
		}
		nsm_interactive_gallery.prep = null;
		// on add row
		Matrix.bind('nsm_interactive_gallery', 'display', function(cell){
			var use_canvas = Number(String(cell.field.id).substring(9));
			var this_id = cell.field.id +'_'+ cell.col.id +'_'+ cell.row.id;
			var $d = cell.dom.$inputs;
			var dimensions = {
				"top":Number($d.eq(0).val()),
				"left":Number($d.eq(1).val()),
				"width":Number($d.eq(2).val()),
				"height":Number($d.eq(3).val()),
				"zIndex":50
			}
			nsm_interactive_gallery.add(use_canvas, cell.field.id, cell.col.id, cell.row.id, this_id, dimensions);
			var select_btn = cell.dom.$td.find('button.nsm_ig_select');
			select_btn.bind('click', function(event){
				event.preventDefault();
				var id = cell.field.id+'_'+cell.row.id+'_'+cell.col.id;
				nsm_interactive_gallery.select(id);
				return false;
			});
		});
		// on delete row
		Matrix.bind('nsm_interactive_gallery', 'remove', function(cell){
		  $("#"+cell.row.id).remove();
		});
		
		return false;
	},
	createCanvas:function(canvas){
		var upload_dir_id = $("input[name='field_id_"+(canvas.target_field)+"_hidden_dir']").val();
		var upload_dir = EE.upload_directories[upload_dir_id]['url'];
		var image_name = $("input[name='field_id_"+(canvas.target_field)+"_hidden']").val();
		var image_url = upload_dir + image_name;
		// assemble image html and prepend to table
		var image_html = $('<img class="nsm_ig_canvas" src="'+image_url+'" alt="large image" id="field_id_'+canvas.this_field+'_image"/>');
		// prepare the construction site
		canvas.container_id = 'field_id_'+canvas.this_field+'_ui_container';
		canvas.zone = $('<div class="nsm_ig_container" id="'+canvas.container_id+'"></div>').prependTo( $("#field_id_"+canvas.this_field+'') );
		// append image and fix width to image size
		canvas.zone.prepend(image_html);
		// tell self that image is ready
		canvas.ready = true;
		nsm_interactive_gallery.canvases[ canvas.this_field ] = canvas;
	},
	add: function(use_canvas, field_id, col_id, row_id, new_id, new_dimensions){
		// default
		var dimensions = {
			"top":5,
			"left":5,
			"width":150,
			"height":100,
			"zIndex":50
		}
		// merged
		if(typeof(new_dimensions) !== 'undefined'){
			for(p in dimensions){
				if(typeof(new_dimensions) !== 'undefined' && new_dimensions[p] > 0){
					dimensions[p] = new_dimensions[p];
				}
			}
		}
		var canvas = nsm_interactive_gallery.canvases[ use_canvas ].zone;
		// create new selection box
		var $s = $('<div/>')
					.attr('id', field_id+'_'+row_id+'_'+col_id)
					.data('nsm_ig', {
						"canvas": use_canvas,
						"field": field_id,
						"row": row_id,
						"col": col_id
					})
					.addClass('nsm_ig_stroke')
					.css(dimensions)
					.css('position', 'absolute')
					.bind('click', function(){
						var id = this.id; 
						nsm_interactive_gallery.select(id);
						return false;
					})
					.draggable({
						stop: function(event, ui) {
							nsm_interactive_gallery.updatePos(event, ui);
						}
					})
					.resizable({
						stop: function(event, ui) {
							nsm_interactive_gallery.updatePos(event, ui);
						}
					})
					.appendTo(canvas);
		return false;
	},
	updatePos:function(event, ui){
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
	},
	select: function(id){
		var $t = $('#'+id);
		var this_id = $t.attr('id');
		var ig_data = $t.data('nsm_ig');
		var brushes = nsm_interactive_gallery.canvases[ ig_data.canvas ].zone.find('div.nsm_ig_stroke');
		brushes.removeClass('active');
		brushes.filter($t).addClass('active');
		return false;
	}
}


$(function(){
	$("input.date").datepicker({ 
		dateFormat: $.datepicker.W3C + EE.date_obj_time,
	});
});
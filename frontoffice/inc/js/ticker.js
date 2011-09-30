var ticker = Class.create({  
	
	initialize: function(container, options) {  
		this.container = container;  
		this.options = Object.extend(options || {},{  
			frequency: 1500,  
			item_frequency: 3000,  
			char_frequency: 20,  
			endBits: ['','']  
		});  
		this.current = 0;  
		this.currentChar = 0;  
		this.startTick();  
	},  
	
	startTick: function() {  
		this.container.each(function(item) {  
			item.hide();  
		});  
		setTimeout(this.onTick.bind(this), this.options.frequency);  
	}, 
	 
	onTick: function() {  
		if(this.currentChar==0) {  
			if (this.current_item) {  
				this.current_item.hide();  
			}  
			this.current_item = this.container[this.current%this.container.length]; 			
			this.current_item.show();			
			this.current_element = this.current_item.firstDescendant()  
			this.current_title = this.current_element.innerHTML;  
			this.current++;  
		}  
	
		this.current_element.innerHTML = this.current_title.substring(0,this.currentChar) + this.options.endBits[this.currentChar&this.options.endBits.length-1];  
		if(this.currentChar==this.current_title.length) {  
			this.current_element.innerHTML = this.current_title.substring(0,this.current_title.length);  
			this.currentChar=0;  
			var t = this.options.item_frequency || 1000;  
		} else {  
			this.currentChar++;  
			var t = this.options.char_frequency || 50;  
		}  
		setTimeout(this.onTick.bind(this),t); 		
	}  
});  
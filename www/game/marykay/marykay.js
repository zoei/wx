// (function(){
  /** Card */
  function Card(id){
  	this.id = id;
  	this.elementId = '#card-'+id;
  	this.state = 0;
  	this.value = Math.round(id/2);

  	var me = this;
  	$(this.elementId).ready(function(){
  		$(me.elementId).bind('click', me, function(){me.turn.apply(me)});
  	});
  }
  Card.prototype = {
  	turn: function(){
  		console.debug('turn card:', this.id);
  		if(this.state === 1){
  			$(this.elementId).removeClass('rotateDown');
  			$(this.elementId).addClass('rotateUp');
  		} else {
  			$(this.elementId).removeClass('rotateUp');
  			$(this.elementId).addClass('rotateDown');
  		}
  		this.state = this.state === 1 ? 0 : 1;
  	}
  };

  var cards = [];
  for(var i=1;i<=16;i++){
    cards.push(new Card(i));
  }
// })();
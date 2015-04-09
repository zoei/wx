(function() {

	/** Card */
	function Card(id) {
		this.id = id;
		this.elementId = '#card-' + id;
		this.state = 0;
		this.value = Math.round(id / 2);

		var me = this;
		$(this.elementId).ready(function() {
			$(me.elementId).click(function() {
				me.turn.apply(me)
			});
		});
	}

	Card.prototype = {
		turn: function() {
			console.debug('turn card:', this.id);
			if (this.state === 1) {
				$(this.elementId).removeClass('rotateUp');
				$(this.elementId).addClass('rotateDown');
			} else if (this.state === 0) {
				$(this.elementId).removeClass('rotateDown');
				$(this.elementId).addClass('rotateUp');
			}
			this.state = this.state === 1 ? 0 : 1;
			if (this.turnCallback) {
				this.turnCallback(this, this.state);
			}
		},
		bindTurnCallback: function(cb) {
			this.turnCallback = cb;
		}
	};

	function CardsMgr() {
		this.cards = [];
		this.turnedUpCard = null;
	}

	CardsMgr.prototype = {
		addCard: function(card) {
			this.cards.push(card);
			var me = this;
			card.bindTurnCallback(function(){me.onCardTurn.apply(me, arguments)});
		},
		onCardTurn: function(card, cardState) {
			if(cardState === 1){
				if(!this.turnedUpCard){
					this.turnedUpCard = card;
				} else if(this.turnedUpCard.value !== card.value){
					var me = this;
					setTimeout(function(){
						me.turnedUpCard.turn();
						card.turn();
						me.turnedUpCard = null;
					}, 1000);
				} else {
					this.turnedUpCard = null;
				}
			}
		}
	};

	var cardMgr = new CardsMgr();
	for (var i = 1; i <= 16; i++) {
		cardMgr.addCard(new Card(i));
	}
})();
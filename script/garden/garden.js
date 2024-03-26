
var stage;


function init(){
	initStage();

	var gfx_plant = new createjs.Bitmap("img/plant/plant_complete.jpg");
	var gfx_pot = new createjs.Bitmap("img/pot/pot_2_filled.png");
	var gfx_light = new createjs.Bitmap("img/light/Sunsys_Lowrider.png");

	var gfx_bg = new createjs.Bitmap("img/room/room.jpg");

	var background = new createjs.Shape();
	background.graphics.beginFill("#FFFFFF").drawRect(0,0,1200,900);


	gfx_plant.x = 150;
	gfx_plant.y = 350;

	gfx_pot.x = 239;
	gfx_pot.y = 720;

	gfx_light.x = 139;
	gfx_light.y = 20;

	gfx_plant.scaleX = 0.4;
	gfx_plant.scaleY = 0.4;

	gfx_pot.scaleX = 0.4;
	gfx_pot.scaleY = 0.4;

	gfx_light.scaleX = 0.8;
	gfx_light.scaleY = 0.8;

	gfx_bg.scaleX = 0.75;
	gfx_bg.scaleY = 0.9;

	stage.addChild(background);
	
	stage.addChild(gfx_bg);

	stage.addChild(gfx_plant);
	stage.addChild(gfx_pot);	
	stage.addChild(gfx_light);

	stage.update();
}


function initStage(){

	stage = new createjs.Stage("canvas-main");

 	createjs.Ticker.addEventListener("tick", _UPDATE);	
	
}




function _UPDATE(event) {
     // Actions carried out each tick (aka frame)
     if (!event.paused) {
         stage.update();
     }
 }


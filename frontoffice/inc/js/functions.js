// ticker
function tick(){
	if(typeof(current) == "undefined") current = 0;
	Effect.Appear($$('#tickernews A')[current], { duration: 0.8 });
	setTimeout("Effect.Fade($$('#tickernews A')["+current+"], { duration: 0.2 });", 6500);
	if($$('#tickernews A')[current+1] != null) current++;
	else current = 0;
	setTimeout("tick();", 8000);
}

var canClick = true;
var getCountSeconds = true;
var countClickEl = 0;
var successEl = 0;
var imgEl;
var img1;
var img2;
var imgSrc1;
var imgSrc2;
var cardEl = $(".card");
var frontimgEl = $(".frontimg");
var seconds = 0;
var choiceEl;
var playBtnEl = $("#playbtn");

// click play now event 
playBtnEl.click(getamount);
//playBtnEl.addEventListener("click", getamount);
//click card event 
cardEl.click(cardClick);
// for (var i=0; i < cardEl.length; i++) {
//     cardEl[i].addEventListener("click",cardClick)
// }


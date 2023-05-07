//get amount of cards
function getamount (){
    choiceEl = $("#cardsamount").val();
    console.log(choiceEl);
    cardEl.slice( 0, choiceEl ).css('display','inline');
    $("#dropdown").hide();
    shuffleCard ();
}

//shuffle cards
function shuffleCard (){
    for (var i= choiceEl-1; i >= 0; i--) {
        var rand = Math.floor(Math.random() * (i + 1));
        [frontimgEl[i].src, frontimgEl[rand].src] = [frontimgEl[rand].src, frontimgEl[i].src]
    }
}

//count seconds from start to end
function incrementSeconds() {
    seconds++;
}

//success function
function delaySuccess () {
    img1.add(img2).find('.frontimg').css('visibility', 'hidden');
    canClick = true;
    successEl +=2;
    gameover();
}

//game over function
function gameover(){
    if (successEl == choiceEl){
        clearInterval(cancel);
        $('.hidden_mesage').show();
        $('.seconds').text("you did it in  " + seconds + "  seconds!");
        $('#btn').click(newgame)
        function newgame(){
                location.reload();
                }
        }
}

//failure function
function delayFailure () {
    img1.find('.frontimg').toggleClass('is-flipped');
    img2.find('.frontimg').toggleClass('is-flipped');
    setTimeout(function() {
        img1.find('.frontimg').hide();
        img2.find('.frontimg').hide();
        img1.find('.backimg').css('display','inline');
        img2.find('.backimg').css('display','inline');

        canClick = true;
        },200);

}

//click on card
function cardClick (){
    if (getCountSeconds == true){
        cancel = setInterval(incrementSeconds, 1000);
    }
    getCountSeconds = false;
    var displayEl = $(this).find('.frontimg');
    
    //save the img's source
    if (displayEl.css('display') !== 'inline' && displayEl.css('visibility') !== 'hidden' && canClick) {
        countClickEl += 1;
        imgEl = $(this);
        if (countClickEl == 1){
            imgSrc1 = imgEl.find('.frontimg').attr("src");
            img1 = imgEl;
        } else if (countClickEl == 2 ){ 
            imgSrc2 = imgEl.find('.frontimg').attr("src");
            img2 = imgEl;
    } 
    
    //from back img to front img 
    $(this).find('.backimg').toggleClass('is-flipped');
    setTimeout(function() {
        imgEl.find('.backimg').hide();
        imgEl.find('.frontimg').css('display','inline');
    },200);

    //check success
    if (imgSrc1 == imgSrc2 && countClickEl == 2){
        canClick = false;
        setTimeout(delaySuccess, 1000);
    } else if (countClickEl === 2 ){
        canClick = false;
        setTimeout(delayFailure, 1500);
    }
    if (countClickEl == 2 ){
        countClickEl = 0;
    }
}
}








var S_VOLUME = 0.3;

function setVolume(vol){
    S_VOLUME = (vol > 0)? (vol <= 1)? vol : 0 : 0;
}

function playAudio(type)
{
    if(! SOUND_MUTED)
    {
        switch(type)
        {
            case "buy":
                var sound_buy = new Audio("sound/ca_ching.mp3");

                sound_buy.volume = S_VOLUME;
                sound_buy.play();
                break;

            case "grow":
                var sound_grow = new Audio("sound/garden/grow.wav");

                sound_grow.volume = S_VOLUME;
                sound_grow.play();
                break;

            case "harvest":
                var sound_harvest = new Audio("sound/garden/harvest.wav");

                sound_harvest.volume = S_VOLUME;
                sound_harvest.play();
                break;
            case "drop":
                var sound_drop = new Audio('sound/drop.mp3');

                sound_drop.volume = S_VOLUME;
                sound_drop.play();
                break;

            case "click":
                var sound_click = new Audio("sound/lab/garden_click_space.wav");

                sound_click.volume = S_VOLUME;
                sound_click.play();
                break;
            case "click_drug":
                var sound_click = new Audio("sound/lab/lab_click_drug.wav");

                sound_click.volume = S_VOLUME;
                sound_click.play();
                break;
            case "click_lab":
                var sound_click = new Audio("sound/lab/lab_click_space.wav");

                sound_click.volume = S_VOLUME;
                sound_click.play();
                break;


            case "open":
                var sound_open = new Audio("sound/lab/lab_click_space.wav");

                sound_open.volume = S_VOLUME;
                sound_open.play();
                break;


            default:
                break;

        }
    }
}
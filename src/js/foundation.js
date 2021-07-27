const seasonSwiperElement = document.querySelector('.swiper-container-foundation');

if(seasonSwiperElement)
{
    const foundation_swiper = new Swiper('.swiper-container-foundation', {
        observer: true,
        observeParents: true,
        autoHeight: true,
        autoplay:true,
        loop:true
    });
}
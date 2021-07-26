function docReady(fn) {
    // see if DOM is already available
    if (document.readyState === "complete" || document.readyState === "interactive") {
        // call on next available tick
        setTimeout(fn, 1);
    } else {
        document.addEventListener("DOMContentLoaded", fn);
    }
}

docReady(function() {

    var highlightChildTab = function(swp)
    {
        jQuery('.tab.child').removeClass('active');
        jQuery('.tab.child[data-index="' + swp.activeIndex + '"]').addClass('active');
    }
    
    var highlightSeasonTab = function(swp)
    {
        jQuery('.tab.season').removeClass('active');
        jQuery('.tab.season[data-index="' + swp.activeIndex + '"]').addClass('active');
    }

    new window.Swiper('.swiper-container-seasons', {
        observer: true,
        observeParents: true,
        hashNavigation: {
            replaceState: true,
            watchState:true
        },
        on: {
            activeIndexChange: highlightSeasonTab,
            init: highlightSeasonTab
        }
    });

    new window.Swiper('.swiper-container-children', {
        observer: true,
        observeParents: true,
        hashNavigation: {
            replaceState: true,
            watchState:true
        },
        on: {
            activeIndexChange: highlightChildTab,
            init: highlightChildTab
        },
        allowTouchMove:false
    });
});

jQuery(document).ready(function()
{
    jQuery('.ec_day').on('click', '.product_option', function(event)
    {
        if(event.target.className.includes('product_option '))
        {
            var childid = jQuery(event.target).attr('data-childid');
            var variationid = jQuery(event.target).attr('data-variationid');
            var action = jQuery(event.target).attr('data-action');
            cart(childid, variationid, action, event.target);
        }
    });

    jQuery('.ec_day').on('click', '.book_session_group', function(event)
    {
        if(event.target.className.includes('book_session_group'))
        {
            jQuery('.product_option').each(function(index, elem)
            {
                if(jQuery(elem).attr('data-sessiongroup')
                    && jQuery(elem).attr('data-sessiongroup') == jQuery(event.target).attr('data-sessiongroup')
                    && jQuery(elem).attr('data-childid') == jQuery(event.target).attr('data-childid')
                    && jQuery(elem).attr('data-action') != 'remove_from_cart'
                    && jQuery(elem).attr('data-action') != 'reset_swap'
                    && jQuery(elem).attr('data-action') != 'swap_in_cart'
                )
                {
                    jQuery(elem).trigger('click');
                }
            });
        }
    });

    //set height of tabs
    jQuery(".tablinks_container").css("height", jQuery('.tablinks').height());
});

function bind_click(elem)
{
    jQuery('.product_options').bind('click', '.product_option', function(event)
    {
        var childid = jQuery(event.target).data('childid');
        var variationid = jQuery(event.target).data('variationid');
        var action = jQuery(event.target).data('action');
        cart(childid, variationid, action, this);
    });
}

function cart(childid, variationid, action, elem)
{
    //console.log(childid);
    //console.log(variationid);
    //console.log(action);

    var data = {
        'childid': childid,
        'variationid': variationid,
        'action' : action
    }

    console.log(data);

    var day_parent = jQuery(elem).parents('.ec_day');

    day_parent.block({
        message: null,
        overlayCSS: {
          background: '#fff',
          opacity: 0.6
        },
        onBlock: function()
        {
            jQuery.ajax({
                type: 'post',
                url: ajax_object.ajax_url,
                data: data,
                dataType: 'JSON',
                async: false,
                beforeSend: function()
                {
        
                },
                complete: function(response)
                {
                    day_parent.unblock();
                    
                    /*
                    jQuery(elem).parent('.product_options').find('.product_option').each(function(index, elem)
                    {
                        bind_click(elem);
                    });
                    */
                    
                },
                success: function(response)
                {
                    jQuery(elem).parent('.product_options').replaceWith(response.content);
        
                }
            });
        } 
      });
    //day_parent.css("opacity", 0.1);


}
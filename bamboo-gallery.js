/************************************************************************************************************/

     var bambooGalleryLightboxZIndex = 20;
     var bambooGalleryLightboxDelay  = 500;
     var bambooGalleryCurrentGallery = 0;
     var bambooGalleryCurrentIndex   = 0;

	jQuery( document ).ready( bambooGalleryInit );

/************************************************************************************************************/

     function bambooGalleryInit() {

          // Inject the lightbox into the page.
          var lightboxHTML = '<div class="bamboo-gallery-lightbox">';
          lightboxHTML += '<a class="bamboo-gallery-lightbox-close-button" href="javascript:bambooGalleryHide();">x</a>';
          lightboxHTML += '<a class="bamboo-gallery-lightbox-prev-button" href="javascript:bambooGalleryPrev()">A</a>';
          lightboxHTML += '<div class="bamboo-gallery-lightbox-image"></div>';
          lightboxHTML += '<div class="bamboo-gallery-lightbox-loader"></div>';
          lightboxHTML += '<a class="bamboo-gallery-lightbox-next-button" href="javascript:bambooGalleryNext()">D</a>';
          lightboxHTML += '</div>';
          jQuery( 'body' ).append( lightboxHTML );

     }

/************************************************************************************************************/

     function bambooGalleryShow( gallery, index )
     {

          // Store the current gallery and index.
          bambooGalleryCurrentGallery = gallery;
          bambooGalleryCurrentIndex   = index;

          // Hide the image and show the loader.
          jQuery( '.bamboo-gallery-lightbox-image' ).css( 'opacity', 0 );
          jQuery( '.bamboo-gallery-lightbox-loader' ).css( 'opacity', 1 );

          // Show the lightbox.
          jQuery( '.bamboo-gallery-lightbox' ).css( 'zIndex', bambooGalleryLightboxZIndex );
          jQuery( '.bamboo-gallery-lightbox' ).css( 'opacity', 1 );

          // Get the URL of the image to show.
          var button = jQuery( '#bamboo-gallery-' + gallery + ' .bamboo-gallery-button' )[index-1];
          var url = jQuery( button ).attr( 'data-src' );

          // Create an image.
          var loader = new Image();

          // When then image has loaded...
          jQuery( loader ).load( function(){

               // Set the image in the lightbox.
               jQuery( '.bamboo-gallery-lightbox-image' ).css( 'backgroundImage', 'url(' + url + ')' );

               // Hide the loader and show the image.
               window.setTimeout( function(){
                    jQuery( '.bamboo-gallery-lightbox-loader' ).css( 'opacity', 0 );
                    jQuery( '.bamboo-gallery-lightbox-image' ).css( 'opacity', 1 );
               }, bambooGalleryLightboxDelay );
          } );

          // Load the image.
          loader.src = url;

     }

/************************************************************************************************************/

     function bambooGalleryPrev() {

          // Get the index of the last image in the gallery.
          var lastIndex = jQuery( '#bamboo-gallery-' + bambooGalleryCurrentGallery + ' .bamboo-gallery-button' ).length;

          // Update the current index.
          bambooGalleryCurrentIndex--;
          if( bambooGalleryCurrentIndex==0 ) {
               bambooGalleryCurrentIndex = lastIndex;
          }

          // Show the new image.
          bambooGalleryShow( bambooGalleryCurrentGallery, bambooGalleryCurrentIndex );

     }

/************************************************************************************************************/

     function bambooGalleryNext() {

          // Get the index of the last image in the gallery.
          var lastIndex = jQuery( '#bamboo-gallery-' + bambooGalleryCurrentGallery + ' .bamboo-gallery-button' ).length;

          // Update the current index.
          bambooGalleryCurrentIndex++;
          if( bambooGalleryCurrentIndex > lastIndex ) {
               bambooGalleryCurrentIndex = 1;
          }

          // Show the new image.
          bambooGalleryShow( bambooGalleryCurrentGallery, bambooGalleryCurrentIndex );

     }

/************************************************************************************************************/

	function bambooGalleryHide() {

          // Hide the lightbox.
          jQuery( '.bamboo-gallery-lightbox' ).css( 'zIndex', -1 );
          jQuery( '.bamboo-gallery-lightbox' ).css( 'opacity', 0 );

	}

/************************************************************************************************************/
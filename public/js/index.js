// max interval due to ngrok limitations
var POLL_INTERVAL = 5*1000;

function ReceiveMMSController(){
  function showEnvVarUnsetWarning() {
    alert('Please be sure to set your environment variables. Similar to those found in the .env file in the root of this repository.');
  }

  function init() {
    return $.get('/api/config')
      .then(function(res){
        if (!res.twilioNumber) {
            return showEnvVarUnsetWarning();
        }

        var phoneNumber = res.twilioNumber.replace(/(\+\d{1})(\d{3})(\d{3})(\d{4})/, '$1($2)-$3-$4')
        $('.twilio-number').html(phoneNumber);

        pollForIncomingImages();
      })
  }

  function showImages() {
    var $imgContainer = $('.image-container');
    $.get('/api/media')
      .then(function(images) {
        for (var i = 0; i < images.length; i++) {
          var filename = images[i].filename;
          if (filename.indexOf('.jpeg') > -1) {
            $imgContainer.append('<img width="100%" class="col-md-4" data-filename="'+ filename +'" src="/api/media/'+ filename +'"/>');
          } else {
            $imgContainer.append('<a width="100%" class="pane col-md-4" data-filename="'+ filename +'" href="/api/media/'+ filename+'">' +
              filename + '</a>');
          }
        }
      });
  }

  function pollForIncomingImages() {
    setInterval(showImages, POLL_INTERVAL);
  }

  return {
    init: init,
  }
}

$(document).ready(function(){
  var receiveMMSController = ReceiveMMSController();
  receiveMMSController.init();
});

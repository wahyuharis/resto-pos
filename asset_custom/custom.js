function SubmitLoading(attr) {
    var self = this;
    this.loading_content = ''+ 
    '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...'+
    '';
    this.default_content = "";
    this.attr=attr;
    
    this.set_attr=function(attr){
        self.attr=attr;
    }

    this.write = function () {
        self.default_content = $(self.attr).html();
        $(self.attr).html(self.loading_content);
        $(self.attr).prop("disabled", true);
    }

    this.rewrite = function () {
//        self.default_content = "";
        $(self.attr).html(self.default_content);
        $(self.attr).prop("disabled", false);

    }
    
    this.write_html=function(){
        $(self.attr).html(self.loading_content);
    }
    

}
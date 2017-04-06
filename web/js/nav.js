$( document ).ready(function() {
    if($( ".ulHome > li:first-child").hover)
    {
        $(".logoGrey").addClass(".logoGreyHover");
    }
    else
    {
        $(".logoGrey").removeClass(".logoGreyHover");
    }
});

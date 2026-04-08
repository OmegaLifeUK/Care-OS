$(document).on('click','#saveClientDols',function(){
    var dols_status = $("#dols_status").val();
    if(dols_status == ''|| dols_status == undefined){
        $("#dols_status").css('border','1px solid red').focus();
        return false;
    }else{
        $("#dols_status").css('border','');
        var data = new FormData($("#clientDolsForm")[0]);
        data.append('client_id', client_id);
        $.ajax({
            type: "POST",
            url: saveDolsUrl,
            data: data,
            async: false,
            contentType: false,
            cache: false,
            processData: false,
            success: function (response) {
                console.log(response);
                if (typeof isAuthenticated === "function") {
                    if (isAuthenticated(response) == false) {
                        return false;
                    }
                } 
                if(response.success === true){
                    $("#clientDolsForm")[0].reset();
                    $(".dolsSectionFirst").hide();
                    $(".dolsSectionSecond").show();
                    $('.ajax-alert-suc').show();
                    $('.msg').text(response.message);
                    showDolsList();
                    setTimeout(function(){
                        $(".notification-box").fadeOut();
                        $('.msg').text("");
                    }, 5000);
                }
            },
            error: function (xhr, status, error) {
                var errorMessage = xhr.status + ': ' + xhr.statusText;
                alert('Error - ' + errorMessage + "\nMessage: " + error);
            }
        });
    }
});
function showDolsList(pageUrl = dolsListUrl){
    $.ajax({
        type: "POST",
        url: pageUrl,
        data: {client_id:client_id,_token:token},
        success: function (response) {

            console.log(response);
            // return false;
            if (typeof isAuthenticated === "function") {
                if (isAuthenticated(response) == false) {
                    return false;
                }
            }

            if (response.success === true) {
                var data=response.data.data;
                // console.log(data);return false;
                var table=document.getElementById('dolsRenderList');
                if(data.length === 0){
                    table.innerHTML="Data Not Found";
                }else{
                    table.innerHTML='';
                    let tableData = '';
                    data.forEach(function(val, key) {
                        let referral_date = moment(val.referral_date).format('MMMM Do, YYYY');
                        let start_date = moment(val.authorisation_start_date).format('MMMM Do, YYYY');
                        let end_date = moment(val.authorisation_end_date).format('MMMM Do, YYYY');
                        let badgeColor  = badgeColors(val.dols_status);
                        tableData += `<div class="planCard borderleftPurple">
                                    <div class="planTop">
                                        <div class="planTitle">
                                            <span class="roundTag ${badgeColor}">${val.dols_status}</span>
                                            <span class="inactive roundTag">${val.authorisation_type}</span>
                                        </div>
                                        <div class="planActions">
                                            <button class="addDolsRecordBtn" data-formtype="edit" data-dols_status="${val.dols_status}" data-authorisation_type="${val.authorisation_type}" data-referral_date="${val.referral_date}" data-authorisation_start_date="${val.authorisation_start_date}" data-authorisation_end_date="${val.authorisation_end_date}" data-review_date="${val.review_date}" data-supervisory_body="${val.supervisory_body}" data-case_reference="${val.case_reference}" data-best_interests_assessor="${val.best_interests_assessor}" data-mental_health_assessor="${val.mental_health_assessor}" data-reason_for_dols="${val.reason_for_dols}" data-imca_appointed="${val.imca_appointed}" data-mental_capacity_assessment="${val.mental_capacity_assessment}" data-appeal_rights="${val.appeal_rights}" data-care_plan_updated="${val.care_plan_updated}" data-family_notified="${val.family_notified}" data-additional_notes="${val.additional_notes}" data-id="${val.id}"><i class='bx  bx-edit'></i> </button>
                                            <!-- <button class="danger"><i class="bx  bx-trash"></i> </button> -->
                                        </div>
                                    </div>`;
                                    if(val.referral_date != null || val.authorisation_start_date != null){
                                        tableData += `<div class="planMeta">`;
                                        if(val.referral_date != null){
                                            tableData += `<div><strong>Referral Date: </strong> ${referral_date}</div>`;
                                        }if(val.authorisation_start_date != null){
                                           tableData += `<div><strong>Start Date: </strong> ${start_date}</div>`;
                                        }
                                        tableData += `</div>`;
                                    }if(val.authorisation_end_date !=null || val.supervisory_body !=null){
                                        tableData += `<div class="planMeta">`;
                                        if(val.authorisation_end_date !=null){
                                            tableData += `<div><strong>End Date: </strong> ${end_date}</div>`;
                                        }if(val.supervisory_body !=null){
                                            tableData += `<div><strong>Supervisory Body: </strong> ${truncateText(val.supervisory_body)}</div>`;
                                        }
                                        tableData += `</div>`;
                                    }if(val.case_reference !=null){
                                    tableData += `<div class="planFooter">
                                        <span><strong> Case Reference: </strong> ${truncateText(val.case_reference)}</span>
                                    </div>`;
                                    }if(val.reason_for_dols !=null){
                                    tableData += `<div class="medicationSheet">
                                        <div class="reasonBox">
                                            <strong>Reason:</strong>
                                            ${truncateText(val.reason_for_dols)}
                                        </div>
                                    </div>`;
                                    }
                                tableData += `</div>`;
                    });

                    $("#dolsRenderList").html(tableData);
                    var paginationControls = $("#dolsPagination");
                    paginationControls.empty();
                    if (response.data.prev_page_url) {
                        paginationControls.append('<button class="profileDrop me-3" onclick="showDolsList( \'' + response.data.prev_page_url + '\')">Previous</button>');
                    }
                    if (response.data.next_page_url) {
                        paginationControls.append('<button class="profileDrop" onclick="showDolsList( \'' + response.data.next_page_url + '\')">Next</button>');
                    }
                }
            } else {
                alert("Something went wrong");
                return false;
            }
        },
        error: function (xhr, status, error) {
            var errorMessage = xhr.status + ': ' + xhr.statusText;
            alert('Error - ' + errorMessage + "\nMessage: " + error);
        }
    });
}
function badgeColors(dols_status){
    if(dols_status == "Not Applicable"){
        return "muteBadges";
    }else if(dols_status == "Screening Required"){
        return "yellowBadges";
    }else if(dols_status == "Application Submitted"){
        return "buleBadges";
    }else if(dols_status == "Standard Authorisation Granted"){
        return "greenbadges";
    }else if(dols_status == "Urgent Authorisation Granted"){
        return "highBadges";
    }else if(dols_status == "Not Authorised" || dols_status == "Expired"){
        return "redbadges";
    }else if(dols_status == "Under Review"){
        return "purpleBadges";
    }else{
        return "muteBadges";
    }
}
$(document).on('change','.dolsCheckbox',function(){
    if($(this).is(':checked')){
        $(this).val(1);
    }else{
        $(this).val(0);
    }
});
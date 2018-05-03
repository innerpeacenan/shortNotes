jobList = $("#main > div.job-box > div.job-list > ul > li")
console.log(jobList)
jobList.each(function () {
    var jobid = $(this).find("a").data('jobid')
    if (jobid == 13179008) {
        console.log($(this).css('background-color','black'))
    }
})


$.ajax({
    type: 'POST',
    url: 'http://www.note.com/item/get-items',
    data: {fid: 0},
    success: function (result) {
        var data = result.data;
        if (Array.isArray(data) && data.length === 0) {
           l(data);
        }
    }
});
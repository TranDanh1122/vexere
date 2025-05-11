function convertToSlug(text) {
  return text.toLowerCase()
    .replace(/ /g, "-")
    .replace(/[^\w-]+/g, "");
}

function generateToc() {

    if($('.mce-toc').find('ul li').length) {
        $('.mce-toc').remove()
    }
    if($('.sudo-toc').find('ul li').length) {
        $('.sudo-toc').remove()
    }
    // Lấy thẻ cha "single-content"
    const postContent = document.getElementById('single-content');

    if (!postContent) {
        console.error('Không tìm thấy thẻ "single-content" trong văn bản.');
        return;
    }

    // Lấy danh sách các thẻ h2, h3, h4, h5 trong thẻ cha "single-content"
    const headings = postContent.querySelectorAll('h2, h3');
    // Tạo menu chứa các mục từ các thẻ
    const toc = document.createElement('ul');
    let currentParentHeading = null;
    let currentList = null;
    let tmp = false;
    for (let i = 0; i < headings.length; i++) {
        const heading = headings[i];
        const headingText = heading.textContent;
        if (!heading.id) {
            heading.id = 'heading-' + i;
        } else {
            heading.id = convertToSlug(heading.id) + '-' + i
        }
        const nodeName = heading.nodeName.toLowerCase();

        if (nodeName == 'h2') {
            // Tạo thẻ cha mới và thêm vào menu toc
            const parentListItem = document.createElement('li');
            const parentLink = document.createElement('a');
            parentLink.textContent = headingText;
            parentLink.href = '#' + heading.id;
            parentLink.setAttribute('data-scroll', '#' + heading.id);

            parentListItem.classList.add('content-heading');
            parentListItem.appendChild(parentLink);
            toc.appendChild(parentListItem);

            // Tạo danh sách con mới cho thẻ cha
            const childList = document.createElement('div');
            childList.classList.add('toc-list__child');
            parentListItem.appendChild(childList);

            // Đặt thẻ cha hiện tại và danh sách con hiện tại
            currentParentHeading = heading;
            currentList = childList;
            tmp = true;
        } else {
            if(tmp === false) {
                // Tạo thẻ cha mới và thêm vào menu toc
                const parentListItem = document.createElement('li');
                const parentLink = document.createElement('a');
                parentLink.textContent = headingText;
                parentLink.href = '#' + heading.id;
                parentLink.setAttribute('data-scroll', '#' + heading.id);
                parentListItem.appendChild(parentLink);
                toc.appendChild(parentListItem);
            } else {
                // Thêm thẻ con vào danh sách con của thẻ cha hiện tại
                if (currentParentHeading && currentList && headingText) {
                    const childListItem = document.createElement('div');
                    const childLink = document.createElement('a');
                    childLink.textContent = headingText;
                    childLink.href = '#' + heading.id;
                    childLink.setAttribute('data-scroll', '#' + heading.id);
                    childListItem.appendChild(childLink);
                    currentList.appendChild(childListItem);
                }
            }
        }
  }
  // Tạo phần tử header
  const headerHTML = `<div id="toc-lists__header" class="toc-lists__header flex-center-between">
        <div class="flex-center-left">
            <span class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" height="1.3em" viewBox="0 0 512 512"><path d="M61.77 401l17.5-20.15a19.92 19.92 0 0 0 5.07-14.19v-3.31C84.34 356 80.5 352 73 352H16a8 8 0 0 0-8 8v16a8 8 0 0 0 8 8h22.83a157.41 157.41 0 0 0-11 12.31l-5.61 7c-4 5.07-5.25 10.13-2.8 14.88l1.05 1.93c3 5.76 6.29 7.88 12.25 7.88h4.73c10.33 0 15.94 2.44 15.94 9.09 0 4.72-4.2 8.22-14.36 8.22a41.54 41.54 0 0 1-15.47-3.12c-6.49-3.88-11.74-3.5-15.6 3.12l-5.59 9.31c-3.72 6.13-3.19 11.72 2.63 15.94 7.71 4.69 20.38 9.44 37 9.44 34.16 0 48.5-22.75 48.5-44.12-.03-14.38-9.12-29.76-28.73-34.88zM496 224H176a16 16 0 0 0-16 16v32a16 16 0 0 0 16 16h320a16 16 0 0 0 16-16v-32a16 16 0 0 0-16-16zm0-160H176a16 16 0 0 0-16 16v32a16 16 0 0 0 16 16h320a16 16 0 0 0 16-16V80a16 16 0 0 0-16-16zm0 320H176a16 16 0 0 0-16 16v32a16 16 0 0 0 16 16h320a16 16 0 0 0 16-16v-32a16 16 0 0 0-16-16zM16 160h64a8 8 0 0 0 8-8v-16a8 8 0 0 0-8-8H64V40a8 8 0 0 0-8-8H32a8 8 0 0 0-7.14 4.42l-8 16A8 8 0 0 0 24 64h8v64H16a8 8 0 0 0-8 8v16a8 8 0 0 0 8 8zm-3.91 160H80a8 8 0 0 0 8-8v-16a8 8 0 0 0-8-8H41.32c3.29-10.29 48.34-18.68 48.34-56.44 0-29.06-25-39.56-44.47-39.56-21.36 0-33.8 10-40.46 18.75-4.37 5.59-3 10.84 2.8 15.37l8.58 6.88c5.61 4.56 11 2.47 16.12-2.44a13.44 13.44 0 0 1 9.46-3.84c3.33 0 9.28 1.56 9.28 8.75C51 248.19 0 257.31 0 304.59v4C0 316 5.08 320 12.09 320z"/></svg>            </span>
            <p>${$('#single-content').data('title')}</p>
        </div>
        <p class="close" aria-label="Expand or collapse"></p>
        </div>`;

    if(toc.childNodes.length !== 0) {
        // Tạo phần tử <div> để bọc menu toc
        const tocWrapper = document.createElement('div');
        tocWrapper.classList.add('toc-lists');
        tocWrapper.id = "toc-lists";
        // Thêm nội dung header vào phần tử <div> bọc
        tocWrapper.innerHTML = headerHTML;

        // Chèn menu toc vào bên trong phần tử <div> bọc
        tocWrapper.appendChild(toc);
        // Chèn phần tử <div> vào trước thẻ h2 đầu tiên
        const firstHeading = postContent.querySelector('h2');
        const firstHeading3 = postContent.querySelector('h3');
        if(positionToc == 1) {
            if (firstHeading) {
                $(firstHeading).before(tocWrapper)
            } else if (firstHeading3) {
                $(firstHeading3).before(tocWrapper)
            }
        } else if(positionToc == 2) {
            if (firstHeading) {
                $(firstHeading).after(tocWrapper)
            } else if (firstHeading3) {
                $(firstHeading3).after(tocWrapper)
            }
        } else if(positionToc == 3) {
            $('#single-content').prepend(tocWrapper)
        } else if(positionToc == 4) {
            $('#single-content').append(tocWrapper)
        }
    }
    var menuHeading = document.querySelectorAll('#toc-lists .content-heading');
    menuHeading.forEach(function(heading) {
        const tocChild = heading.querySelector('.toc-list__child');
        if (tocChild && tocChild.hasChildNodes()) {
            const afterIcon = document.createElement('p');
            afterIcon.classList.add('after-icon');
            heading.insertBefore(afterIcon, heading.firstChild);
        }
    });
}
function isParentHeading(headingText) {
  const regex = /^\d+[\.\-\+]\s/; // Match one or more digits followed by a dot or a hyphen and a space
  return regex.test(headingText);
}

// Gọi hàm để sinh menu
generateToc();

function checkEmpty(value) {
    if (value == null) {
        return true;
    } else if (value == 'null') {
        return true;
    } else if (value == undefined) {
        return true;
    } else if (value == '') {
        return true;
    } else {
        return false;
    }
}

$('body').on('click','*[data-scroll]',function(e) {
    e.preventDefault();
    id = $(this).data('scroll');
    offset_top = $(this).data('top');
    if (!checkEmpty(offset_top)) {
        offset_top = $(id).offset().top-offset_top;
    } else {
        offset_top = $(id).offset().top;
    }
    $('html, body').animate({scrollTop: offset_top - 80}, 500);
});

$('body').on('click','#toc-lists p.close',function(e) {
    e.preventDefault();
    $(this).toggleClass('active')
    $(this).closest('#toc-lists').find('> ul').slideToggle()
});
$('body').on('click', '.content-heading .after-icon', function(e){
    e.preventDefault();
    $(this).toggleClass('active');
    $(this).closest('.content-heading').find('.toc-list__child').slideToggle();
});

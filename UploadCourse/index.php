<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses</title>
    
    <link rel="stylesheet" href="nav.css">
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
</head>
<body>
<header class="header">
        <nav class="navigation">
            <div class="nav-links">
            <a href="#" class="nav-link">Home</a>
            <a href="#" class="nav-link" id="coursesLink">My Leaning</a>
            <a href="#" class="nav-link">History</a>
            </div>
        <div class="nav-icons">
            <box-icon name='bell' color='#fff' type="solid" size="max(1.5vw, 20px)" class="bell-icon" class="nav-icon"></box-icon>
            <box-icon name='user-circle' color='#fff' type="solid" size="max(1.5vw, 20px)" onclick="togglePopup(event)"class="nav-icon"></box-icon>

            <!-- Popup Profile -->
            <div class="popup" id="popup">
                <div class="popup-content">
                <box-icon name='x'class="close" onclick="closePopup(event)"></box-icon>
                    <div class="userpicname">
                        <div class="userpic"></div>
                        <div class="username"><?php echo $name;?></div>
                    </div>
                    <hr class="divider">
                    <div class="settings">
                        <div class="editprofile" onclick="OpenandCloseEdit(event)"><box-icon name='edit' color='#000' class="settingsIcons" onclick="OpenandCloseEdit(event)"></box-icon><p class="IconLink" onclick="OpenandCloseEdit(event)">Edit Profile</p></div>

                        <div class="lightdark">
                           <button class="light"><box-icon name='sun' type='solid' color='#ffad00' class="settingsIcons sun"></box-icon><p class="IconText">Light</p></button>
                           <button class="dark"><box-icon name='moon' type='solid' color='#000' class="settingsIcons moon"></box-icon><p class="IconText">Dark</p></button>
                        </div>

                        <div class="exitdashboard">
                            <box-icon name='exit' color='#000' type='solid'class="settingsIcons"></box-icon><a class="IconLink" href="#">Exit Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of Popup -->

        </nav>
    </header>


<!-- Search -->
<div class="search-container">
    <div class="wrap">
        <input type="text" id="search-box" placeholder="Search...">
        <box-icon id="search-icon" name="search-alt-2"></box-icon>
    </div>
</div>

<!-- Courses container -->
<div class="course-box">

    <div class="courses-container"  id="courses-container">
    </div>
</div>
<br><br>
<h1 class="rec">RECOMMENDED  INSTRUCTORS FOR THE MONTH OF JULY</h1>

<div class="probox">
        <div class="ins">

        </div>
</div>

<!-- Modal Structure -->
<div id="course-modal" class="modal">
    <div class="modal-content">
        <div id="modal-body" class="mb">
            <box-icon name='x' class="close" onclick="closePopup(event)"></box-icon>
            <img id="modalThumbnail" alt="Course Thumbnail" class="modal-thumbnail">
            <div class="modal-header">
                <h2 class="br" id="modalTitle"></h2>
                <div id="modalRatings" class="rating"></div>
            </div>
            <br>
            <p class="ins" id="modalInstructor"></p>
            <br>
            <p id="modalDifficulty"></p>
            <p id="modalLessons"></p>
            <br>
            <p class="desc">Description</p>
            <p class="descp" id="modalDescription"> Description</p>
        </div>
        <br>
        <button class="enroll-button"><span>Enroll Now !!</span></button>
    </div>
</div>




    <script src="node_modules/jquery/dist/jquery.min.js"></script>
    <script>
$(document).ready(function() {
    function loadCourses() {
        $.ajax({
            url: 'fetch_homepage.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    $('#courses-container').html('<p>' + response.error + '</p>');
                } else {
                    $('#courses-container').empty();
                    response.forEach(course => {
                        const courseContainer = $('<div>').addClass('course-con');
                        const courseDiv = $('<div>').addClass('folder');
                        
                        const courseRatings = course.course_ratings; 
                        let ratingHtml = '<div class="star-rating">';
                        for (let i = 1; i <= 5; i++) {
                            let starType = 'empty'; 
                            if (i <= courseRatings) {
                                starType = 'full'; 
                            }
                            else if (i === Math.floor(courseRatings) + 1 && courseRatings % 1 !== 0) {
                                starType = 'half'; 
                            }
                            
                            ratingHtml += `
                                <input type="radio" id="star${i}" name="rating" value="${i}" class="star-input" style="display: none;">
                                <label for="star${i}" class="star-label ${starType}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="clamp(1rem, 2vw, 1.75rem)" height="clamp(1rem, 2vw, 1.75rem)" viewBox="0 0 24 24" fill="${starType === 'full' ? '#ffb633' : '#e0e0e0'}">
                                        <path d="M12 2.3l2.4 7.4h7.6l-6 4.8 2.3 7.4-6.3-4.7-6.3 4.7 2.3-7.4-6-4.8h7.6z"/>
                                    </svg>
                                </label>`;
                        }
                        ratingHtml += '</div>';

                        courseDiv.html(`
                            <img src="${course.thumbnail}" alt="${course.coursename}" class="course-thumbnail">
                            <h3>${course.coursename}</h3>
                            <p>${course.difficulty}</p>
                            ${ratingHtml}
                        `);

                        courseDiv.click(function() {
                            openCourseModal(
                                course.thumbnail, 
                                course.coursename, 
                                course.instructor, 
                                'Difficulty: ' + course.difficulty, 
                                 course.description, 
                                'Lessons: ' + course.lessons,
                                course.course_ratings // Pass the ratings
                            );
                        });

                        courseContainer.append(courseDiv);
                        $('#courses-container').append(courseContainer);
                    });
                }
            },
            error: function(xhr, status, error) {
                $('#courses-container').html('<p>There was an error fetching the courses.</p>');
                console.error('Error fetching courses:', error);
            }
        });
    }

    function openCourseModal(thumbnail, title, instructor, difficulty, description, lessons, ratings) {
        $('#modalThumbnail').attr('src', thumbnail);
        $('#modalTitle').text(title);
        $('#modalInstructor').text(instructor);
        $('#modalDifficulty').text(difficulty);
        $('#modalDescription').text(description);
        $('#modalLessons').text(lessons);

        let ratingHtml = '<div class="star-rating">';
        for (let i = 1; i <= 5; i++) {
            let starType = 'empty';
            if (i <= ratings) {
                starType = 'full';
            } else if (i === Math.floor(ratings) + 1 && ratings % 1 !== 0) {
                starType = 'half';
            }
            ratingHtml += `
                <label class="star-label ${starType}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="clamp(1rem, 2vw, 1.75rem)" height="clamp(1rem, 2vw, 1.75rem)" viewBox="0 0 24 24" fill="${starType === 'full' ? '#ffb633' : '#e0e0e0'}">
                        <path d="M12 2.3l2.4 7.4h7.6l-6 4.8 2.3 7.4-6.3-4.7-6.3 4.7 2.3-7.4-6-4.8h7.6z"/>
                    </svg>
                </label>`;
        }
        ratingHtml += '</div>';

        $('#modalRatings').html(ratingHtml);

        $('#course-modal').show();
        $('body').addClass('no-scroll');
    }

    $('.close').click(function() {
        $('#course-modal').hide();
        $('body').removeClass('no-scroll');
    });

    $(window).click(function(event) {
        if ($(event.target).is('#course-modal')) {
            $('#course-modal').hide();
            $('body').removeClass('no-scroll');
        }
    });

    loadCourses();
});


    </script> 
</body>
</html>

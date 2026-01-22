<!-- Minimal Bootstrap Modal -->
<div class="modal fade" id="pmsEditTaskModal" tabindex="-1" aria-labelledby="cardDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <h5 class="modal-title d-flex align-items-center gap-2 mb-0" id="pmsEditTaskTitle">
                        <i class="far fa-circle"></i>
                        EVSS Intern Name
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

 <div class="modal-body">
    <div class="task-container">
        <!-- ===== ALL TOGGLES (MUST COME FIRST) ===== -->
<input type="checkbox" id="labelToggle" hidden>
<input type="checkbox" id="createLabelToggle" hidden>
<input type="checkbox" id="memberToggle" hidden>
<input type="checkbox" id="pmProfileToggle" hidden>
<input type="checkbox" id="dateToggle" hidden>
<input type="checkbox" id="dateSavedToggle" hidden>
<input type="checkbox" id="checklistToggle" hidden>
<input type="checkbox" id="attachToggle" hidden>


    <!-- Task Summary -->
  <div class="task-summary">
    <div class="task-name">
      Task Name <i class="fa-solid fa-pen"></i>
    </div>

    <div class="task-right">
      <!-- <div class="status">
  <span class="status-dot"></span>
  <span>Status :</span>Active
</div> -->

      <div class="tasks">
        <i class="fa-solid fa-list"></i> <span>Tasks :</span> 04
      </div>
    </div>
  </div>

   <!-- ATTACHMENT TOGGLE (IMPORTANT) -->
  <input type="checkbox" id="attachToggle" hidden>
 

  <!-- Action Bar -->
  <div class="actions">
    <div class="action-item">
       <label for="labelToggle">
    <i class="fa-solid fa-tag"></i> Labels
    </label>
    <div class="divider"></div>
  </div>

  <div class="action-item">
    <label for="dateToggle"><i class="fa-regular fa-calendar"></i> Dates</label>
    <div class="divider"></div>
  </div>

  <div class="action-item">
    <label for="checklistToggle"><i class="fa-solid fa-list-check"></i> Checklist</label>
    <div class="divider"></div>
  </div>

  <div class="action-item">
    <label for="memberToggle"><i class="fa-solid fa-users"></i> Members</label>
      <div class="divider"></div>
  </div>

  <div class="action-item">
  <label for="attachToggle">
    <span>
      <i class="fa-solid fa-paperclip"></i> Attachment
    </span>
  </label>
</div>
</div>



<!-- LABEL PANEL -->
<div class="label-panel">

  <div class="label-panel-header">
    <span>Labels</span>
    <label for="labelToggle"><i class="fa-solid fa-xmark"></i></label>
  </div>

  <label for="createLabelToggle" class="create-label-btn">
    + Create a new label
  </label>

</div>

<!-- CREATE LABEL PANEL -->
<div class="create-label-panel">

  <div class="label-panel-header">
    <label for="createLabelToggle"><i class="fa-solid fa-arrow-left"></i></label>
    <span>Create label</span>
    <label for="labelToggle"><i class="fa-solid fa-xmark"></i></label>
  </div>

  <div class="label-preview"></div>

  <input type="text" placeholder="Title" class="label-title">

  <div class="color-grid">
    <span class="color c1"></span>
    <span class="color c2"></span>
    <span class="color c3"></span>
    <span class="color c4"></span>
    <span class="color c5"></span>
  </div>

  <button class="create-btn">Create</button>

</div>

<div class="member-panel">

  <div class="member-header">
    <span>Members</span>
    <label for="memberToggle">
      <i class="fa-solid fa-xmark"></i>
    </label>
  </div>

  <input type="text" placeholder="Search members" class="member-search">

  <p class="member-sub">Board members</p>

  <!-- MEMBER -->
  <label class="member-item">
    <input type="checkbox" hidden checked>
    <span class="avatar pm">PM</span>
    <span>Prensha Mhrzn</span>
  </label>

  <label class="member-item">
    <input type="checkbox" hidden>
    <span class="avatar as">AS</span>
    <span>Ajay Singh</span>
  </label>

  <label class="member-item">
    <input type="checkbox" hidden>
    <span class="avatar rs">RS</span>
    <span>Rikesh Shakya</span>
  </label>
</div>

<!-- DATE PANEL -->
<div class="date-panel">

  <div class="date-header">
    <span>Select Date</span>
    <label for="dateToggle">
      <i class="fa-solid fa-xmark"></i>
    </label>
  </div>

  <input type="date">

  <div class="date-actions">

  <label for="dateToggle" class="cancel-date">Cancel</label>
<label for="dateSavedToggle" class="save-date">Save</label>


</div>


</div>

<!-- Checklist Panel  -->
<div class="checklist-panel">

  <div class="checklist-header">
    <span>Add Checklist</span>
    <label for="checklistToggle"><i class="fa-solid fa-xmark"></i></label>
  </div>

  <!-- TITLE -->
  <input type="text" id="checklistTitle" placeholder="Checklist title">

  <!-- ITEMS -->
  <div id="checklistItems">
    <label class="check-item">
      <input type="checkbox">
      <input type="text" placeholder="Item 1">
    </label>
  </div>

  <!-- ACTIONS -->
  <div class="checklist-actions">
    <button id="addItem">Add</button>
    <button class="cancel" onclick="closeChecklist()">Cancel</button>
    <button class="save" onclick="saveChecklist()">Save</button>
  </div>

</div>

<!-- META ROW -->
<div class="meta-row">

  <!-- LABELS -->
  <div class="label-output">
    <span class="label-title-text">Labels:</span>

    <div class="task-label">
      Copy Request
      <i class="fa-solid fa-xmark"></i>
    </div>

    <label for="labelToggle" class="add-label-btn">
      <i class="fa-solid fa-plus"></i>
    </label>
  </div>
   <!-- MEMBERS -->
 <div class="member-output">



  <span class="label-title-text">Members:</span>

  <!-- AVATAR (CLICK THIS) -->
  <label for="pmProfileToggle" class="avatar pm">
    PM
  </label>

  <!-- ADD BUTTON -->
  <label for="memberToggle" class="add-member">+</label>

  <!-- PROFILE CARD (DIRECT SIBLING) -->
  <div class="member-profile-card">
    <strong>Prensha Maharjan</strong>
    <p>UI / UX Intern</p>

    <label for="pmProfileToggle" class="remove-member">
      Remove from card
    </label>
  </div>

</div>
  <!-- DATES -->
<input type="checkbox" id="dateSavedToggle" hidden>

<div class="date-output">
  <span class="label-title-text">Date:</span>

  <span class="date-chip">
    12 Jan, 2026
    <label for="dateToggle" class="date-arrow">
      <i class="fa-solid fa-chevron-down"></i>
    </label>
  </span>
</div>
</div>
  <!-- Description -->
   <div class="description-section">
  <div class="section-title">
    <i class="fa-solid fa-bars"></i> Description
  </div>
  <textarea placeholder="Add any extra details..."></textarea>
  </div>

  <!-- Attachment Section (Hidden by default) -->
   
  <div class="attachment-section">
    <div class="section-title">
      <i class="fa-solid fa-paperclip"></i> Attachments
    </div>

    <div class="attachment-box">
      <i class="fa-solid fa-upload"></i>
      <span>Drop files here or click to upload</span>
    </div>
  </div>

  <!-- Accordion -->
  <details open class="accordion">
    <summary class="accordion-header">
  <span class="accordion-title">UI / UX Design</span>
  <span class="accordion-actions">
   <i class="fa-regular fa-trash-can"></i>

    <i class="fa-solid fa-chevron-down"></i>
  </span>
</summary>


    <div class="accordion-body">

      <!-- Task Row 1 -->
      <div class="task-row">
        <input type="checkbox" checked>
        <div class="task-text">Completed UI design HTML & CSS</div>

        <input type="checkbox" id="m1" class="menu-toggle">
        <label for="m1" class="menu-btn">
          <i class="fa-solid fa-ellipsis-vertical"></i>
        </label>

        <div class="menu">
          <div><i class="fa-solid fa-pen"></i> Edit</div>
          <div><i class="fa-regular fa-trash-can"></i> Delete</div>
          <div><i class="fa-solid fa-users"></i> Member</div>
          
        </div>
      </div>

      <!-- Task Row 2 -->
      <div class="task-row">
        <input type="checkbox">
        <div class="task-text">Create wireframes for new project</div>

        <input type="checkbox" id="m2" class="menu-toggle">
        <label for="m2" class="menu-btn">
          <i class="fa-solid fa-ellipsis-vertical"></i>
        </label>

        <div class="menu">
          <div><i class="fa-solid fa-pen"></i> Edit</div>
          <div><i class="fa-regular fa-trash-can"></i> Delete</div>
          <div>
    <i class="fa-solid fa-users"></i> Member
  </div>
        </div>
      </div>

      <!-- Add Task Toggle -->
      <input type="checkbox" id="addTaskToggle" hidden>

      <!-- Add Task Input -->
      <div class="add-task-box">
        <input type="text" placeholder="Enter new task..." />
      </div>

<!-- DUE DATE TOGGLE -->
<input type="checkbox" id="dueDateToggle" hidden>

<!-- Due Date Picker -->
<div class="due-date-box">
  <div class="due-date-header">
    <i class="fa-solid fa-calendar"></i>
    <span>Select Due Date</span>
  </div>

  <div class="due-date-action">
    <input type="date">
    <button class="add-date">Add</button>
  </div>
</div>

      <!-- Footer -->
      <div class="footerhere">
        <div>
          <label for="addTaskToggle" class="add">
            <i class="fa-solid fa-plus"></i> Add New Task
          </label>

          <label for="dueDateToggle" class="due">

            <i class="fa-regular fa-calendar"></i> Due Date
          </label>
        </div>

        <div class="progress-here" style="--value:65">
          <span></span>
          <b>65%</b>
        </div>
      </div>

    </div>
  </details>
  <div id="savedChecklists"></div>

  <!-- Comments -->
  <div class="comments-card">

    <div class="comments-header">
      <div class="title">
        <i class="fa-regular fa-comment-dots"></i>
        <span>Comments</span>
      </div>
      <button class="details-btn">Show Details</button>
    </div>

    <hr>

    <div class="comment-box">
      <label>Leave a Comment</label>
      <div class="comment-input">
        <input type="text" placeholder="Write a comment..." />
        <div class="comment-actions">
          <span class="attach">
            <i class="fa-solid fa-paperclip"></i> Attach file
          </span>
          <button class="post-btn">Post Comment</button>
        </div>
      </div>
    </div>

    <div class="comment-item">
      <img src="https://i.pravatar.cc/50?img=32" alt="avatar">
      <div class="comment-content">
        <div class="comment-top">
          <strong>Prensha Maharjan</strong>
          <div class="comment-icons">
            <i class="fa-regular fa-thumbs-up"></i>
            <i class="fa-regular fa-thumbs-down"></i>
            <span class="reply">Reply</span>
          </div>
        </div>
        <p>One Task is added, and other two are completed.</p>
        <small>15 min ago</small>
      </div>
    </div>

       <div class="comment-item">
      <img src="https://i.pravatar.cc/50?img=32" alt="avatar">
      <div class="comment-content">
        <div class="comment-top">
          <strong>Prensha Maharjan</strong>
          <div class="comment-icons">
            <i class="fa-regular fa-thumbs-up"></i>
            <i class="fa-regular fa-thumbs-down"></i>
            <span class="reply">Reply</span>
          </div>
        </div>
        <p>One Task is added, and other two are completed.</p>
        <small>15 min ago</small>
      </div>
    </div>

  </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
let itemCount = 1;

document.getElementById("addItem").onclick = () => {
  itemCount++;
  const item = document.createElement("label");
  item.className = "check-item";
  item.innerHTML = `
    <input type="checkbox">
    <input type="text" placeholder="Item ${itemCount}">
  `;
  document.getElementById("checklistItems").appendChild(item);
};

function closeChecklist() {
  document.getElementById("checklistToggle").checked = false;
}

function saveChecklist() {
  const title = document.getElementById("checklistTitle").value;
  if (!title) return;

  const items = document.querySelectorAll("#checklistItems input[type='text']");
  let listHTML = "";

  items.forEach(i => {
    if (i.value.trim()) {
      const menuId = `menu_${Date.now()}_${Math.random()}`;

listHTML += `
  <div class="task-row">
    <input type="checkbox">

    <div class="task-text">${i.value}</div>

    <input type="checkbox" id="${menuId}" class="menu-toggle">
    <label for="${menuId}" class="menu-btn">
      <i class="fa-solid fa-ellipsis-vertical"></i>
    </label>

    <div class="menu">
      <div><i class="fa-solid fa-pen"></i> Edit</div>
      <div><i class="fa-regular fa-trash-can"></i> Delete</div>
      <div><i class="fa-solid fa-users"></i> Member</div>
      
    </div>
  </div>
`;

    }
  });

  const card = document.createElement("details");
  card.className = "accordion";
  card.open = true;
  card.innerHTML = `
    <summary class="accordion-header">
  <span class="accordion-title">${title}</span>

  

  <span class="accordion-actions">
    <i class="fa-solid fa-trash"></i>
    <i class="fa-solid fa-chevron-down"></i>
  </span>
</summary>

    <div class="accordion-body">${listHTML}
       <div class="footer">
      <div>
        <label class="add">
          <i class="fa-solid fa-plus"></i> Add New Task
        </label>

        <label class="due">
          <i class="fa-solid fa-calendar"></i> Due Date
        </label>
      </div>

      <div class="progress-here">
        <span></span>
        <b>0%</b>
      </div>
    </div>

      
      </div>
  `;

  document.getElementById("savedChecklists").appendChild(card);
  closeChecklist();
}
</script>



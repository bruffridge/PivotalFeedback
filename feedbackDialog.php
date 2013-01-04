<div class="modal hide" id="feedbackDialog" role="dialog" aria-labelledby="feedbackTitle">
  <div class="modal-header">
    <button type="button" class="close" aria-label="Close">&times;</button>
    <h3 id="feedbackTitle">Got Feedback?</h3>
  </div>
  <form name="feedbackForm" id="feedbackForm" action="" method="post">
  <div class="modal-body">

    <div class="formItem first">
      <label for="<?php echo $fieldNames['feedbackForm']['type']; ?>">Issue type</label>
      <select name="<?php echo $fieldNames['feedbackForm']['type']; ?>" id="<?php echo $fieldNames['feedbackForm']['type']; ?>">
        <option value="bug">Bug</option>
        <option value="feature">Feature Request</option>
        <option value="chore">Other</option>
      </select>
      <span id="msg-<?php echo $fieldNames['feedbackForm']['type']; ?>" class="err">&nbsp;</span>
    </div>

    <div class="formItem newline">
      <label for="<?php echo $fieldNames['feedbackForm']['description']; ?>">What happened?</label>
      <textarea name="<?php echo $fieldNames['feedbackForm']['description']; ?>" id="<?php echo $fieldNames['feedbackForm']['description']; ?>" class="width400 height6em" maxlength="2000"></textarea>
      <span id="msg-<?php echo $fieldNames['feedbackForm']['description']; ?>" class="err">&nbsp;</span>
    </div>

    <div class="formItem newline">
      <label for="<?php echo $fieldNames['feedbackForm']['steps']; ?>">Steps needed to reproduce this bug</label>
      <textarea name="<?php echo $fieldNames['feedbackForm']['steps']; ?>" id="<?php echo $fieldNames['feedbackForm']['steps']; ?>" class="width400 height6em" maxlength="2000"></textarea>
      <span id="msg-<?php echo $fieldNames['feedbackForm']['steps']; ?>" class="err">&nbsp;</span>
    </div>
    
    <div class="formItem newline">
      *Your name, system specs, time, and URL will be submitted automatically.
    </div>

  </div>
  <div class="modal-footer">
    <button type="submit" name="submit" value="Leave feedback" id="feedbackBtn" class="btn btn-primary">Leave feedback</button>
    <button type="button" name="cancel" value="Cancel" id="cancelBtn" class="btn">Cancel</button>
  </div>
  </form>
</div>
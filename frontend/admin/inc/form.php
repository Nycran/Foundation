<form role="form">
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <label class="control-label">Title</label>
                <input type="text" class="form-control" placeholder="Title" />
            </div>
            <div class="form-group">
                <label class="control-label">Text field disabled</label>
                <input type="text" class="form-control" placeholder="Lorem ipsum" disabled />
            </div>
            <div class="form-group">
                <label class="control-label">Dropdown</label>
                <select class="form-control">
                    <option>- Please select</option>
                </select>
            </div>
            <div class="checkbox">
                <label><input type="checkbox" /> Enable this item</label>
            </div>
            <div class="checkbox">
                <label><input type="checkbox" /> Set as featured</label>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group has-error">
                <label class="control-label">Text field with error</label>
                <input type="text" class="form-control" placeholder="Lorem ipsum" />
            </div>
            <div class="form-group">
                <label class="control-label">Textarea</label>
                <textarea class="form-control" placeholder="Textarea" rows="4"></textarea>
                <p class="help-block">Help block with instruction...</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label class="control-label">Multiple Select</label>
                <select multiple class="form-control">
                    <option>1</option>
                    <option>2</option>
                    <option>3</option>
                    <option>4</option>
                    <option>5</option>
                </select>
            </div>
            <div class="form-group">
                <label class="control-label">Radio Group</label>
                <div>
                    <label class="radio-inline"><input type="radio" name="radio" checked /> Yes</label>
                    <label class="radio-inline"><input type="radio" name="radio" /> No</label>
                </div>
            </div>
        </div>
    </div>
    
    <p>
        <button class="btn btn-primary"><i class="fa fa-save"></i> <strong>Save Changes</strong></button>
    </p>
</form>
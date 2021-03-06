import React from "react";

const Field = ({
  name,
  label,
  labelClass = "",
  value,
  onChange,
  placeholder = "",
  type = "text",
  error = "",
  disabled = "",
  moreClass = " ",
}) => (
  <div className="form-group">
    <label className={labelClass} htmlFor={name}>
      {label}
    </label>
    <input
      value={value}
      onChange={onChange}
      type={type}
      placeholder={placeholder || label}
      name={name}
      id={name}
      className={"form-control" + (error && " is-invalid") + " " + moreClass}
      disabled={disabled}
    />
    {error && <p className="invalid-feedback">{error}</p>}
  </div>
);

export default Field;

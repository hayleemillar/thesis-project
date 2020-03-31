import React, { Component } from "react";
import { render } from "react-dom";

class SignIn extends Component {
  constructor(props) {
    super(props);
    this.state = {email:'', passw:''};

    this.handleSubmit = this.handleSubmit.bind(this);
    this.handleEmailChange = this.handleEmailChange.bind(this);
    this.handlePasswChange = this.handlePasswChange.bind(this);
  }

  componentDidCatch(error, errorInfo) {
    // You can also log the error to an error reporting service
    logErrorToMyService(error, errorInfo);
  }

  handleEmailChange(event) {
    this.setState({email: event.target.value});
  }

  handlePasswChange(event) {
    this.setState({password: event.target.value});
  }

  handleSubmit(event) {
    fetch("/signin/post",
      {
        method: "POST",
        cache: "no-cache",
        headers: {
          "content_type": "application/json"
        },
        body: JSON.stringify({user: this.state.email, passw: this.state.passw}),
      })
      .then(res => {
        if (res.status > 400) {
            console.log(res.json());
            return res.json();
        }
      })
      .then((res) => {
        console.log(res);
        event.preventDefault();
        // TODO TODO TODO
        // this.setState({ redirect: "/user" });
      });
  }

  render() {
    return (
      <form onSubmit={this.handleSubmit}>
        <label>
          E-mail:
          <input type="email" placeholder="johndoe@site.com" onChange={this.handleEmailChange} required/>
        </label>
        <label>
          Password:
          <input type="text" placeholder="password" onChange={this.handlePasswChange} required/>
        </label>
        <input type="submit" value="Submit" />
      </form>
    );
  }
}

export default SignIn;

const container = document.getElementById("signin-app");
render(<SignIn />, container);
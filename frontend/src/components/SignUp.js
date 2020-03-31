import React, { Component } from "react";
import { render } from "react-dom";

class SignUp extends Component {
  constructor(props) {
    super(props);
    this.state = {name:'', email:'', passw:''};

    this.handleSubmit = this.handleSubmit.bind(this);
    this.handleNameChange = this.handleNameChange.
    this.handleEmailChange = this.handleEmailChange.bind(this);
    this.handlePasswChange = this.handlePasswChange.bind(this);
  }

  handleNameChange(event) {
      this.setState({name: event.target.value});
  }

  handleEmailChange(event) {
    this.setState({email: event.target.value});
  }

  handlePasswChange(event) {
    this.setState({password: event.target.value});
  }

  handleSubmit(event) {
    fetch("api/thesis/signin/post",
      {
        method: "POST",
        cache: "no-cache",
        headers: {
          "content_type": "application/json"
        },
        body: JSON.stringify({
          name: this.state.name,
          user: this.state.email, 
          passw: this.state.passw
        }),
      })
      .then(res => {
        if (res.status > 400) {
            console.log(res.json());
            return res.json();
        }
      })
      .then((res) => {
        console.log(res);
        // TODO TODO TODO
        // this.setState({ redirect: "/signin" });
      });
  }

  render() {
    return (
      <form onSubmit={this.handleSubmit}>
        <label>
          Name:
          <input type="text" placeholder="John Doe" onChange={this.handleNameChange} required/>
        </label>
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

export default SignUp;

const container = document.getElementById("signup-app");
render(<SignUp />, container);